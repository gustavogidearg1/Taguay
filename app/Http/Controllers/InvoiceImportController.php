<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InvoiceImportController extends Controller
{
    public function index()
    {
        return view('invoices.import');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'pdfs' => ['required', 'array'],
            'pdfs.*' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB por archivo
        ]);

        $disk = env('INVOICE_INPUT_DISK', 'local');
        $path = env('INVOICE_INPUT_PATH', 'facturas');

        foreach ($request->file('pdfs') as $file) {
            $originalName = time() . '_' . $file->getClientOriginalName();
            Storage::disk($disk)->putFileAs($path, $file, $originalName);
        }

        return back()->with('success', 'Los PDFs se subieron correctamente.');
    }

    public function process()
    {
        $disk = env('INVOICE_INPUT_DISK', 'local');
        $inputPath = env('INVOICE_INPUT_PATH', 'facturas');
        $outputPath = env('INVOICE_OUTPUT_PATH', 'exports/facturas.csv');

        $files = Storage::disk($disk)->files($inputPath);

        if (empty($files)) {
            return back()->with('error', 'No hay archivos PDF en la carpeta.');
        }

        $rows = [];

        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf') {
                continue;
            }

            try {
                $result = $this->extractInvoiceDataFromPdf($disk, $file);

                $rows[] = [
                    'archivo'            => basename($file),
                    'proveedor'          => $result['proveedor'] ?? '',
                    'cuit'               => $result['cuit'] ?? '',
                    'tipo_comprobante'   => $result['tipo_comprobante'] ?? '',
                    'punto_venta'        => $result['punto_venta'] ?? '',
                    'numero'             => $result['numero'] ?? '',
                    'fecha'              => $result['fecha'] ?? '',
                    'moneda'             => $result['moneda'] ?? '',
                    'subtotal'           => $result['subtotal'] ?? '',
                    'iva'                => $result['iva'] ?? '',
                    'total'              => $result['total'] ?? '',
                    'observaciones'      => $result['observaciones'] ?? '',
                    'estado'             => 'OK',
                ];
            } catch (\Throwable $e) {
                $rows[] = [
                    'archivo'            => basename($file),
                    'proveedor'          => '',
                    'cuit'               => '',
                    'tipo_comprobante'   => '',
                    'punto_venta'        => '',
                    'numero'             => '',
                    'fecha'              => '',
                    'moneda'             => '',
                    'subtotal'           => '',
                    'iva'                => '',
                    'total'              => '',
                    'observaciones'      => $e->getMessage(),
                    'estado'             => 'ERROR',
                ];
            }
        }

        $this->generateCsv($disk, $outputPath, $rows);

        return back()->with('success', 'Proceso finalizado. Ya podés descargar el CSV.');
    }

    public function download()
    {
        $disk = env('INVOICE_INPUT_DISK', 'local');
        $outputPath = env('INVOICE_OUTPUT_PATH', 'exports/facturas.csv');

        if (!Storage::disk($disk)->exists($outputPath)) {
            return back()->with('error', 'Todavía no existe el CSV generado.');
        }

        return Storage::disk($disk)->download($outputPath);
    }

    private function extractInvoiceDataFromPdf(string $disk, string $file): array
    {
        $apiKey = env('OPENAI_API_KEY');
        $model = env('OPENAI_MODEL', 'gpt-4o');

        if (!$apiKey) {
            throw new \Exception('Falta OPENAI_API_KEY en el .env');
        }

        $absolutePath = Storage::disk($disk)->path($file);

        // 1) Subir archivo a OpenAI
        $uploadResponse = Http::withToken($apiKey)
            ->attach('file', fopen($absolutePath, 'r'), basename($absolutePath))
            ->post('https://api.openai.com/v1/files', [
                'purpose' => 'user_data',
            ]);

        if (!$uploadResponse->successful()) {
            throw new \Exception('Error subiendo PDF a OpenAI: ' . $uploadResponse->body());
        }

        $fileId = $uploadResponse->json('id');

        if (!$fileId) {
            throw new \Exception('No se obtuvo file_id del archivo subido.');
        }

        // 2) Crear respuesta estructurada
        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/responses', [
                'model' => $model,
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => 'Extraé los datos de esta factura.
Devuelve solo información del documento.
Si un dato no aparece, devolver null.
No inventes datos.'
                            ],
                            [
                                'type' => 'input_file',
                                'file_id' => $fileId,
                            ],
                        ],
                    ],
                ],
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'invoice_extraction',
                        'strict' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'proveedor' => ['type' => ['string', 'null']],
                                'cuit' => ['type' => ['string', 'null']],
                                'tipo_comprobante' => ['type' => ['string', 'null']],
                                'punto_venta' => ['type' => ['string', 'null']],
                                'numero' => ['type' => ['string', 'null']],
                                'fecha' => ['type' => ['string', 'null']],
                                'moneda' => ['type' => ['string', 'null']],
                                'subtotal' => ['type' => ['number', 'null']],
                                'iva' => ['type' => ['number', 'null']],
                                'total' => ['type' => ['number', 'null']],
                                'observaciones' => ['type' => ['string', 'null']],
                            ],
                            'required' => [
                                'proveedor',
                                'cuit',
                                'tipo_comprobante',
                                'punto_venta',
                                'numero',
                                'fecha',
                                'moneda',
                                'subtotal',
                                'iva',
                                'total',
                                'observaciones',
                            ],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error consultando Responses API: ' . $response->body());
        }

        $outputText = $response->json('output.0.content.0.text');

        if (!$outputText) {
            throw new \Exception('La respuesta no devolvió texto estructurado.');
        }

        $decoded = json_decode($outputText, true);

        if (!is_array($decoded)) {
            throw new \Exception('La respuesta no es un JSON válido.');
        }

        return $decoded;
    }

    private function generateCsv(string $disk, string $outputPath, array $rows): void
    {
        $headers = [
            'archivo',
            'proveedor',
            'cuit',
            'tipo_comprobante',
            'punto_venta',
            'numero',
            'fecha',
            'moneda',
            'subtotal',
            'iva',
            'total',
            'observaciones',
            'estado',
        ];

        $stream = fopen('php://temp', 'r+');

        fputcsv($stream, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($stream, [
                $row['archivo'] ?? '',
                $row['proveedor'] ?? '',
                $row['cuit'] ?? '',
                $row['tipo_comprobante'] ?? '',
                $row['punto_venta'] ?? '',
                $row['numero'] ?? '',
                $row['fecha'] ?? '',
                $row['moneda'] ?? '',
                $row['subtotal'] ?? '',
                $row['iva'] ?? '',
                $row['total'] ?? '',
                $row['observaciones'] ?? '',
                $row['estado'] ?? '',
            ], ';');
        }

        rewind($stream);
        $csvContent = stream_get_contents($stream);
        fclose($stream);

        Storage::disk($disk)->put($outputPath, $csvContent);
    }
}
