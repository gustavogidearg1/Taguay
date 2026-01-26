<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Campania;
use App\Models\Cultivo;
use App\Models\Moneda;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContratoNotificacion as ContratoNotificacionMail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContratoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /* =========================
       INDEX (filtro + paginación)
    ========================== */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 20, 50, 100], true)) $perPage = 15;

        [$query, $q, $sort, $dir] = $this->buildIndexQuery($request);

        $contratos = $query->paginate($perPage)->appends([
            'q' => $q,
            'per_page' => $perPage,
            'sort' => $sort,
            'dir' => $dir,
        ]);

        return view('contratos.index', compact('contratos', 'q', 'perPage', 'sort', 'dir'));
    }

    /* =========================
       CREATE
    ========================== */
    public function create()
    {
        $campanias = Campania::query()->where('activo', true)->orderByDesc('id')->get();
        $cultivos  = Cultivo::query()->orderBy('name')->get();
        $monedas   = Moneda::query()->orderBy('name')->get();

        // ✅ Organizaciones (solo activas)
        $organizaciones = Organizacion::query()
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        $defaultVendedor = 'Taguay';

        $defaultMonedaId = Moneda::query()
            ->where('name', 'Dolar')
            ->orWhere('name', 'Dólar')
            ->value('id');

        // Si tu _form usa estos arrays, dejalos (para evitar undefined)
        $optCaracteristica = [
            'PRECIO_HECHO' => 'Precio hecho',
            'A_FIJAR'      => 'A fijar',
            'CONDICIONAL'  => 'Condicional',
        ];
        $optFormacion = [
            'A_COBRAR'     => 'A cobrar',
            'CON_ANTICIPO' => 'Con anticipo',
            'EN_CANJE'     => 'En canje',
            'FORWARD'      => 'Forward',
        ];
        $optCondicionPrecio = [
            'ENTREGA_OBL' => 'Entrega obligatoria',
            'WASHOUT'     => 'Washout',
        ];
        $optCondicionPago = [
            'A_COBRAR'     => 'A cobrar',
            'CON_ANTICIPO' => 'Con anticipo',
            'EN_CANJE'     => 'En canje',
            'NO_SE_COBRA'  => 'No se cobra',
        ];
        $optListaGrano = [
            'ABIERTA' => 'Abierta',
            'CERRADA' => 'Cerrada',
            'CAMARA' => 'Camara',
        ];
        $optDestino = [
            'GRANO'      => 'Grano',
            'OTRO_GRANO' => 'Otro grano',
        ];
        $optFormato = [
            'FORWARD'     => 'Forward',
            'DISPONIBLE'  => 'Disponible',
        ];
        $optDisponibleTipo = [
            'PRECIO_HECHO' => 'Precio hecho',
            'A_FIJAR'      => 'A fijar',
        ];

        return view('contratos.create', compact(
            'campanias',
            'cultivos',
            'monedas',
            'organizaciones',
            'defaultVendedor',
            'optCaracteristica',
            'optFormacion',
            'optCondicionPrecio',
            'optCondicionPago',
            'optListaGrano',
            'optDestino',
            'optFormato',
            'optDisponibleTipo',
            'defaultMonedaId'
        ));
    }

    /* =========================
       STORE ✅ (incluye sub_contratos)
    ========================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nro_contrato' => ['required', 'numeric'],
            'num_forward'  => ['nullable', 'numeric'],

            'fecha' => ['required', 'date'],
            'entrega_inicial' => ['nullable', 'date'],
            'entrega_final'   => ['nullable', 'date'],

            'campania_id' => ['required', 'exists:campanias,id'],
            'cultivo_id'  => ['required', 'exists:cultivos,id'],
            'moneda_id'   => ['required', 'exists:monedas,id'],

            // ✅ Organización
            'organizacion_id' => ['required', 'exists:organizaciones,id'],

            'caracteristica_precio' => ['required', 'string', 'max:50'],
            'formacion_precio'      => ['required', 'string', 'max:50'],
            'condicion_precio'      => ['required', 'string', 'max:50'],
            'condicion_pago'        => ['required', 'string', 'max:50'],
            'lista_grano'           => ['required', 'string', 'max:20'],

            'vendedor' => ['nullable', 'string', 'max:120'],

            'destino' => ['required', 'string', 'max:30'],
            'formato' => ['required', 'string', 'max:30'],
            'disponible_tipo' => ['nullable', 'string', 'max:30'],

            'definicion' => ['nullable', 'string'],

            'cantidad_tn' => ['required', 'numeric', 'min:0'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'precio_fijado' => ['nullable', 'numeric', 'min:0'],

            'comision'  => ['nullable', 'numeric', 'min:0'],
            'paritaria' => ['nullable', 'numeric', 'min:0'],
            'volatil'   => ['nullable', 'numeric', 'min:0'],

            'obs'        => ['nullable', 'string', 'max:200'],
            'importante' => ['nullable', 'string', 'max:200'],

            // Sub tabla precio fijación
            'sub_contratos' => ['nullable', 'array'],
            'sub_contratos.*.fecha' => ['required', 'date'],
            'sub_contratos.*.toneladas' => ['nullable', 'numeric', 'min:0'],
            'sub_contratos.*.nuevo_precio_fijacion' => ['nullable', 'numeric', 'min:0'],
            'sub_contratos.*.observacion' => ['nullable', 'string', 'max:100'],
        ]);

        $data['vendedor'] = trim((string)($data['vendedor'] ?? '')) !== '' ? $data['vendedor'] : 'Taguay';

        $subRows = $data['sub_contratos'] ?? [];
        unset($data['sub_contratos']);

        $contrato = Contrato::create($data);

        foreach ($subRows as $row) {
            if (empty($row['fecha'])) continue;

            $contrato->subContratos()->create([
                'fecha' => $row['fecha'],
                'toneladas' => $row['toneladas'] ?? 0,
                'nuevo_precio_fijacion' => $row['nuevo_precio_fijacion'] ?? 0,
                'observacion' => $row['observacion'] ?? null,
            ]);
        }

        $this->sendContratoMail($contrato->fresh(), 'creado');

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato creado correctamente.');
    }

    /* =========================
       SHOW (imprimible)
    ========================== */
    public function show(Contrato $contrato)
    {
        $contrato->load(['campania', 'cultivo', 'moneda', 'organizacion', 'subContratos']);
        return view('contratos.show', compact('contrato'));
    }

    /* =========================
       EDIT
    ========================== */
    public function edit(Contrato $contrato)
    {
        $contrato->load(['subContratos']);

        $campanias = Campania::query()->orderByDesc('id')->get();
        $cultivos  = Cultivo::query()->orderBy('name')->get();
        $monedas   = Moneda::query()->orderBy('name')->get();

        // ✅ Organizaciones (solo activas)
        $organizaciones = Organizacion::query()
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        $defaultVendedor = 'Taguay';

        // mismas opciones que create
        $optCaracteristica = [
            'PRECIO_HECHO' => 'Precio hecho',
            'A_FIJAR'      => 'A fijar',
            'CONDICIONAL'  => 'Condicional',
        ];
        $optFormacion = [
            'A_COBRAR'     => 'A cobrar',
            'CON_ANTICIPO' => 'Con anticipo',
            'EN_CANJE'     => 'En canje',
            'FORWARD'      => 'Forward',
        ];
        $optCondicionPrecio = [
            'ENTREGA_OBL' => 'Entrega obligatoria',
            'WASHOUT'     => 'Washout',
        ];
        $optCondicionPago = [
            'A_COBRAR'     => 'A cobrar',
            'CON_ANTICIPO' => 'Con anticipo',
            'EN_CANJE'     => 'En canje',
        ];
        $optListaGrano = [
            'ABIERTA' => 'Abierta',
            'CERRADA' => 'Cerrada',
            'CAMARA' => 'Camara',
        ];
        $optDestino = [
            'GRANO'      => 'Grano',
            'OTRO_GRANO' => 'Otro grano',
        ];
        $optFormato = [
            'FORWARD'     => 'Forward',
            'DISPONIBLE'  => 'Disponible',
        ];
        $optDisponibleTipo = [
            'PRECIO_HECHO' => 'Precio hecho',
            'A_FIJAR'      => 'A fijar',
        ];

        return view('contratos.edit', compact(
            'contrato',
            'campanias',
            'cultivos',
            'monedas',
            'organizaciones',
            'defaultVendedor',
            'optCaracteristica',
            'optFormacion',
            'optCondicionPrecio',
            'optCondicionPago',
            'optListaGrano',
            'optDestino',
            'optFormato',
            'optDisponibleTipo'
        ));
    }

    /* =========================
       UPDATE ✅ (sincroniza sub_contratos)
    ========================== */
    public function update(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'nro_contrato' => ['required', 'numeric'],
            'num_forward'  => ['nullable', 'numeric'],

            'fecha' => ['required', 'date'],
            'entrega_inicial' => ['nullable', 'date'],
            'entrega_final'   => ['nullable', 'date'],

            'campania_id' => ['required', 'exists:campanias,id'],
            'cultivo_id'  => ['required', 'exists:cultivos,id'],
            'moneda_id'   => ['required', 'exists:monedas,id'],

            // ✅ Organización
            'organizacion_id' => ['required', 'exists:organizaciones,id'],

            'caracteristica_precio' => ['required', 'string', 'max:50'],
            'formacion_precio'      => ['required', 'string', 'max:50'],
            'condicion_precio'      => ['required', 'string', 'max:50'],
            'condicion_pago'        => ['required', 'string', 'max:50'],
            'lista_grano'           => ['required', 'string', 'max:20'],

            'vendedor' => ['nullable', 'string', 'max:120'],

            'destino' => ['required', 'string', 'max:30'],
            'formato' => ['required', 'string', 'max:30'],
            'disponible_tipo' => ['nullable', 'string', 'max:30'],

            'definicion' => ['nullable', 'string'],

            'cantidad_tn' => ['nullable', 'numeric', 'min:0'],
            'precio'      => ['nullable', 'numeric', 'min:0'],
            'precio_fijado' => ['nullable', 'numeric', 'min:0'],

            'comision'  => ['nullable', 'numeric', 'min:0'],
            'paritaria' => ['nullable', 'numeric', 'min:0'],
            'volatil'   => ['nullable', 'numeric', 'min:0'],

            'obs'        => ['nullable', 'string', 'max:200'],
            'importante' => ['nullable', 'string', 'max:200'],

            // sub contratos
            'sub_contratos' => ['nullable', 'array'],
            'sub_contratos.*.id' => ['nullable', 'integer'],
            'sub_contratos.*.fecha' => ['required', 'date'],
            'sub_contratos.*.toneladas' => ['nullable', 'numeric', 'min:0'],
            'sub_contratos.*.nuevo_precio_fijacion' => ['nullable', 'numeric', 'min:0'],
            'sub_contratos.*.observacion' => ['nullable', 'string', 'max:100'],
        ]);

        $data['vendedor'] = trim((string)($data['vendedor'] ?? '')) !== '' ? $data['vendedor'] : 'Taguay';

        $subRows = $data['sub_contratos'] ?? [];
        unset($data['sub_contratos']);

        $contrato->update($data);

        // Sync subcontratos
        $idsEnviados = [];

        foreach ($subRows as $row) {
            if (empty($row['fecha'])) continue;

            if (!empty($row['id'])) {
                $sub = $contrato->subContratos()->where('id', $row['id'])->first();
                if ($sub) {
                    $sub->update([
                        'fecha' => $row['fecha'],
                        'toneladas' => $row['toneladas'] ?? 0,
                        'nuevo_precio_fijacion' => $row['nuevo_precio_fijacion'] ?? 0,
                        'observacion' => $row['observacion'] ?? null,
                    ]);
                    $idsEnviados[] = $sub->id;
                }
            } else {
                $sub = $contrato->subContratos()->create([
                    'fecha' => $row['fecha'],
                    'toneladas' => $row['toneladas'] ?? 0,
                    'nuevo_precio_fijacion' => $row['nuevo_precio_fijacion'] ?? 0,
                    'observacion' => $row['observacion'] ?? null,
                ]);
                $idsEnviados[] = $sub->id;
            }
        }

        // borrar los que eliminaron del form
        if (!empty($idsEnviados)) {
            $contrato->subContratos()->whereNotIn('id', $idsEnviados)->delete();
        } else {
            $contrato->subContratos()->delete();
        }

        $this->sendContratoMail($contrato->fresh(), 'actualizado');

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato actualizado correctamente.');
    }

    /* =========================
       DESTROY
    ========================== */
    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')->with('success', 'Contrato eliminado.');
    }

    /* =========================
       Envío de mail (TO + CC desde .env)
    ========================== */
    private function sendContratoMail(Contrato $contrato, string $action): void
{
    try {
        $contrato->load(['campania','cultivo','moneda','organizacion','subContratos']);

        $to = auth()->user()->email;


        // ✅ Podés poner varios: separados por coma o punto y coma
        // Ej: CONTRATOS_MAIL_CC="a@a.com,b@b.com; c@c.com"
        $ccRaw = (string) env('CONTRATOS_MAIL_CC', '');

        $ccList = collect(preg_split('/[;,]+/', $ccRaw))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '' && filter_var($v, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();

        $mail = Mail::to($to);

        if (!empty($ccList)) {
            $mail->cc($ccList); // ✅ array de correos
        }

        $mail->send(new ContratoNotificacionMail($contrato, $action));

    } catch (\Throwable $e) {
        Log::error('Error enviando mail de contrato: '.$e->getMessage(), [
            'contrato_id' => $contrato->id ?? null,
            'to' => $to ?? null,
        ]);
        // si querés que NO rompa la creación del contrato:
        // no relanzamos
    }
}

    private function buildIndexQuery(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $sort = (string) $request->get('sort', 'id');
        $dir  = strtolower((string) $request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // columnas permitidas para ordenar (evita inyección)
        $allowedSort = ['id', 'nro_contrato', 'num_forward', 'fecha', 'organizacion', 'campania', 'cultivo', 'cantidad_tn', 'precio'];


        if (!in_array($sort, $allowedSort, true)) $sort = 'id';

        $query = Contrato::query()
            ->with(['campania', 'cultivo', 'moneda', 'organizacion']);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nro_contrato', 'like', "%{$q}%")
                    ->orWhere('num_forward', 'like', "%{$q}%")
                    ->orWhere('vendedor', 'like', "%{$q}%")
                    ->orWhereHas('organizacion', function ($o) use ($q) {
                        $o->where('name', 'like', "%{$q}%")
                            ->orWhere('codigo', 'like', "%{$q}%");
                    });
            });
        }

        // ✅ orden
        switch ($sort) {
            case 'nro_contrato':
            case 'num_forward':
            case 'fecha':
            case 'id':
            case 'cantidad_tn':
            case 'precio':
                $query->orderBy($sort, $dir);
                break;

            case 'organizacion':
                $query->orderBy(
                    Organizacion::select('name')
                        ->whereColumn('organizaciones.id', 'contratos.organizacion_id'),
                    $dir
                );
                break;

            case 'campania':
                $query->orderBy(
                    Campania::select('name')
                        ->whereColumn('campanias.id', 'contratos.campania_id'),
                    $dir
                );
                break;

            case 'cultivo':
                $query->orderBy(
                    Cultivo::select('name')
                        ->whereColumn('cultivos.id', 'contratos.cultivo_id'),
                    $dir
                );
                break;
        }

        return [$query, $q, $sort, $dir];
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        [$query, $q, $sort, $dir] = $this->buildIndexQuery($request);

        // exportamos todo (sin paginar)
        $rows = $query->get();

        $filename = 'contratos_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // encabezados
            fputcsv($out, ['Nro', 'Forward', 'Fecha', 'Cliente', 'Campaña', 'Cultivo', 'Cantidad_tn', 'Precio', 'Obs'], ';');


            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->nro_contrato,
                    $c->num_forward ?? '',
                    optional($c->fecha)->format('d/m/Y') ?? '',
                    $c->organizacion->name ?? '',
                    $c->campania->name ?? '',
                    $c->cultivo->name ?? '',
                    $c->cantidad_tn ?? '',
                    $c->precio ?? '',
                    $c->obs ?? '',
                ], ';');
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$query, $q, $sort, $dir] = $this->buildIndexQuery($request);

        $contratos = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contratos.export_pdf', [
            'contratos' => $contratos,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('contratos_' . now()->format('Ymd_His') . '.pdf');
    }
}
