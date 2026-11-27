<?php

namespace App\Services\WhatsApp;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envía mensajes por la WhatsApp Cloud API (Graph API de Meta).
 * Es el "camino de vuelta": el webhook recibe, esta clase responde.
 *
 * Documentación: https://developers.facebook.com/docs/whatsapp/cloud-api
 */
class WhatsAppCliente
{
    /** Texto simple. */
    public function enviarTexto(string $para, string $texto): bool
    {
        return $this->enviar($para, [
            'type' => 'text',
            'text' => ['preview_url' => false, 'body' => $texto],
        ]);
    }

    /**
     * Botones de respuesta rápida (máximo 3).
     * $botones = ['id_interno' => 'Texto del botón', ...]
     */
    public function enviarBotones(string $para, string $texto, array $botones): bool
    {
        $lista = [];
        foreach (array_slice($botones, 0, 3, true) as $id => $titulo) {
            $lista[] = ['type' => 'reply', 'reply' => ['id' => (string) $id, 'title' => mb_substr($titulo, 0, 20)]];
        }

        return $this->enviar($para, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $texto],
                'action' => ['buttons' => $lista],
            ],
        ]);
    }

    /**
     * Lista desplegable (máximo 10 opciones en total).
     * $opciones = ['id_interno' => 'Título', ...]
     */
    public function enviarLista(string $para, string $texto, string $boton, array $opciones, string $seccion = 'Opciones'): bool
    {
        $filas = [];
        foreach (array_slice($opciones, 0, 10, true) as $id => $titulo) {
            $filas[] = ['id' => (string) $id, 'title' => mb_substr($titulo, 0, 24)];
        }

        return $this->enviar($para, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $texto],
                'action' => [
                    'button' => mb_substr($boton, 0, 20),
                    'sections' => [['title' => mb_substr($seccion, 0, 24), 'rows' => $filas]],
                ],
            ],
        ]);
    }

    /**
     * Llamada común a la API. Devuelve true si Meta aceptó el mensaje.
     *
     * Celulares de Argentina: WhatsApp los identifica con el 9 (5493534191741), pero a veces Meta
     * solo acepta enviarles en otro formato (error 131030 con números de prueba). En ese caso se prueban
     * los formatos alternativos y el que funciona se recuerda para ese número.
     */
    private function enviar(string $para, array $mensaje): bool
    {
        $cfg = config('services.whatsapp');

        if (empty($cfg['token']) || empty($cfg['phone_number_id'])) {
            Log::channel('whatsapp')->warning('Falta WHATSAPP_TOKEN o WHATSAPP_PHONE_NUMBER_ID en el .env: no se envió el mensaje.');
            return false;
        }

        $claveCache = "whatsapp_destino_{$para}";
        $destino = Cache::get($claveCache, $para);

        $respuesta = $this->llamarApi($cfg, $destino, $mensaje);

        if ($this->esErrorNumeroNoAutorizado($respuesta)) {
            foreach ($this->formatosAlternativosArgentina($para) as $alternativo) {
                if ($alternativo === $destino) {
                    continue;
                }
                $respuesta = $this->llamarApi($cfg, $alternativo, $mensaje);
                if ($respuesta->successful()) {
                    Cache::forever($claveCache, $alternativo);
                    Log::channel('whatsapp')->info('Formato de número que acepta Meta guardado', ['numero' => $para, 'formato' => $alternativo]);
                    break;
                }
            }
        }

        return $respuesta->successful();
    }

    /** Envía y registra en el log. */
    private function llamarApi(array $cfg, string $destino, array $mensaje): Response
    {
        $url = "https://graph.facebook.com/{$cfg['api_version']}/{$cfg['phone_number_id']}/messages";

        $respuesta = Http::withToken($cfg['token'])
            ->timeout(15)
            ->post($url, array_merge([
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $destino,
            ], $mensaje));

        Log::channel('whatsapp')->info('SALIENTE', [
            'para' => $destino,
            'tipo' => $mensaje['type'],
            'estado_http' => $respuesta->status(),
            'respuesta' => $respuesta->json(),
        ]);

        return $respuesta;
    }

    /** Error 131030: "Recipient phone number not in allowed list". */
    private function esErrorNumeroNoAutorizado(Response $respuesta): bool
    {
        return (int) data_get($respuesta->json(), 'error.code') === 131030;
    }

    /**
     * Formatos alternativos para un celular argentino que llega como 549 + 10 dígitos.
     * Ej.: 5493534191741 → 543534191741 (sin el 9) y 54 + área + 15 + número (3534 15 191741, etc.).
     * Como el largo del código de área varía (2, 3 o 4 dígitos), se prueban las tres opciones.
     */
    private function formatosAlternativosArgentina(string $numero): array
    {
        if (! preg_match('/^549(\d{10})$/', $numero, $m)) {
            return [];
        }

        $diezDigitos = $m[1];
        $formatos = ['54' . $diezDigitos];

        foreach ([4, 3, 2] as $largoArea) {
            $formatos[] = '54' . substr($diezDigitos, 0, $largoArea) . '15' . substr($diezDigitos, $largoArea);
        }

        return $formatos;
    }
}
