<?php

namespace App\Http\Controllers;

use App\Services\WhatsApp\WhatsAppCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook de WhatsApp (Cloud API de Meta).
 *
 * GET  /webhook/whatsapp  → Meta verifica que la dirección es nuestra (una sola vez, al configurarla).
 * POST /webhook/whatsapp  → Meta avisa cada mensaje que llega al número.
 *
 * Fase 2: recibe, registra en storage/logs/whatsapp-*.log y responde un saludo.
 * Fase 3: acá se conecta la conversación guiada para cargar Avance de Campo.
 */
class WhatsAppWebhookController extends Controller
{
    /**
     * Verificación del webhook.
     * Meta llama con ?hub.mode=subscribe&hub.verify_token=...&hub.challenge=...
     * (PHP convierte los puntos en guiones bajos: hub_mode, hub_verify_token, hub_challenge).
     * Si el token coincide con el nuestro, hay que devolver el "challenge" tal cual.
     */
    public function verificar(Request $request)
    {
        $modo = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $desafio = $request->query('hub_challenge');

        $esperado = config('services.whatsapp.verify_token');

        if ($modo === 'subscribe' && $esperado && hash_equals((string) $esperado, (string) $token)) {
            Log::channel('whatsapp')->info('Webhook verificado por Meta.');
            return response((string) $desafio, 200)->header('Content-Type', 'text/plain');
        }

        Log::channel('whatsapp')->warning('Intento de verificación rechazado.', ['modo' => $modo]);
        return response('Token de verificación inválido', 403);
    }

    /**
     * Recepción de mensajes.
     * Siempre respondemos 200 rápido: si Meta no recibe 200, reintenta el aviso varias veces.
     */
    public function recibir(Request $request, WhatsAppCliente $whatsapp)
    {
        if (! $this->firmaValida($request)) {
            Log::channel('whatsapp')->warning('Aviso con firma inválida: descartado.');
            return response('Firma inválida', 403);
        }

        $datos = $request->json()->all();
        $valor = data_get($datos, 'entry.0.changes.0.value', []);

        // Los avisos de "estado" (enviado, entregado, leído) no son mensajes: solo se registran
        if (! empty($valor['statuses'])) {
            Log::channel('whatsapp')->debug('ESTADO', $valor['statuses'][0]);
            return response('OK', 200);
        }

        $mensaje = data_get($valor, 'messages.0');
        if (! $mensaje) {
            return response('OK', 200);
        }

        $telefono = $mensaje['from'];                                  // ej.: 5493512345678
        $nombre = data_get($valor, 'contacts.0.profile.name', '');     // nombre de perfil de WhatsApp
        $texto = $this->textoDelMensaje($mensaje);

        Log::channel('whatsapp')->info('ENTRANTE', [
            'id' => $mensaje['id'] ?? null,
            'de' => $telefono,
            'nombre' => $nombre,
            'tipo' => $mensaje['type'] ?? null,
            'texto' => $texto,
        ]);

        // Fase 2: respuesta de prueba para confirmar que el circuito completo funciona
        $saludo = $nombre !== '' ? "¡Hola {$nombre}!" : '¡Hola!';
        $whatsapp->enviarTexto(
            $telefono,
            "{$saludo} Soy el sistema Taguay 🌾\nRecibí tu mensaje: \"{$texto}\".\n\nPronto vas a poder cargar el Avance de Campo por acá."
        );

        return response('OK', 200);
    }

    /** Texto que escribió o eligió el usuario, según el tipo de mensaje. */
    private function textoDelMensaje(array $mensaje): string
    {
        return match ($mensaje['type'] ?? '') {
            'text' => (string) data_get($mensaje, 'text.body', ''),
            'interactive' => (string) (data_get($mensaje, 'interactive.button_reply.title')
                ?? data_get($mensaje, 'interactive.list_reply.title', '')),
            'button' => (string) data_get($mensaje, 'button.text', ''),
            'location' => 'ubicación ' . data_get($mensaje, 'location.latitude') . ',' . data_get($mensaje, 'location.longitude'),
            'image' => '(foto)',
            default => '(' . ($mensaje['type'] ?? 'desconocido') . ')',
        };
    }

    /**
     * Seguridad: Meta firma cada aviso con la "Clave secreta de la app" (cabecera X-Hub-Signature-256).
     * Así nadie más puede mandarnos mensajes falsos haciéndose pasar por Meta.
     * Si todavía no se cargó WHATSAPP_APP_SECRET, se acepta (útil solo para las primeras pruebas).
     */
    private function firmaValida(Request $request): bool
    {
        $secreto = config('services.whatsapp.app_secret');
        if (empty($secreto)) {
            return true;
        }

        $firma = (string) $request->header('X-Hub-Signature-256', '');
        $esperada = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secreto);

        return hash_equals($esperada, $firma);
    }
}
