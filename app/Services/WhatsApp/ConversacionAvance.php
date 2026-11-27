<?php

namespace App\Services\WhatsApp;

use App\Models\Campania;
use App\Models\Cultivo;
use App\Models\Establecimiento;
use App\Models\EstadoCultivo;
use App\Models\Lote;
use App\Models\LoteEstado;
use App\Models\User;
use App\Models\WhatsAppSesion;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Carga de Avance de Campo por WhatsApp (conversación guiada).
 *
 * Es una "máquina de estados": la tabla whatsapp_sesiones recuerda en qué PASO está cada teléfono.
 * Cada mensaje que llega se procesa según ese paso, se guarda la respuesta y se hace la pregunta siguiente.
 *
 * Recorrido:
 *   establecimiento → lote → cultivo → cantidad (kg) → superficie (ha)
 *   → ¿datos del camión? [patente → chofer → número → humedad → propio/socio]
 *   → ¿foto / ubicación? → resumen → [Guardar | Corregir | Cancelar]
 *
 * Automáticos: campaña activa más reciente, fecha de hoy, estado marcado "por defecto" (hoy Cosecha).
 * En cualquier momento: "cancelar" descarta la carga; "avance" empieza una nueva.
 */
class ConversacionAvance
{
    /** Minutos sin actividad después de los cuales la carga en curso se descarta. */
    private const MINUTOS_INACTIVIDAD = 120;

    /** Opciones por página en las listas (WhatsApp admite 10: 9 + "Ver más"). */
    private const POR_PAGINA = 9;

    public function __construct(private WhatsAppCliente $wa)
    {
    }

    /**
     * Punto de entrada: un mensaje entrante ya interpretado.
     * $entrada = ['tipo' => text|interactive|image|location|..., 'texto' => ..., 'id' => id de botón/lista,
     *             'media_id' => ..., 'latitud' => ..., 'longitud' => ...]
     */
    public function procesar(string $telefono, array $entrada): void
    {
        $usuario = User::where('telefono_whatsapp', $telefono)->first();

        if (! $usuario) {
            $this->wa->enviarTexto($telefono,
                "Hola 👋 Este es el canal de *Taguay Campo* para cargar el Avance de Campo.\n\n" .
                "Tu número no está habilitado. Pedile al administrador del sistema que lo agregue en tu usuario.");
            return;
        }

        if (! $usuario->can('ver_agricola')) {
            $this->wa->enviarTexto($telefono, "Hola {$usuario->name}. Tu usuario no tiene el rol *Agrícola*, necesario para cargar el Avance de Campo. Pedíselo al administrador.");
            return;
        }

        $comando = $this->normalizar($entrada['texto'] ?? '');

        // Comandos que funcionan en cualquier momento
        if (in_array($comando, ['cancelar', 'salir'], true) || ($entrada['id'] ?? '') === 'cancelar') {
            WhatsAppSesion::where('telefono', $telefono)->delete();
            $this->wa->enviarTexto($telefono, "❌ Carga cancelada. No se guardó nada.\n\nEscribí *avance* cuando quieras cargar uno nuevo.");
            return;
        }

        $sesion = WhatsAppSesion::where('telefono', $telefono)->first();

        // Carga abandonada hace mucho: se descarta
        if ($sesion && $sesion->updated_at->lt(now()->subMinutes(self::MINUTOS_INACTIVIDAD))) {
            $sesion->delete();
            $sesion = null;
            $this->wa->enviarTexto($telefono, '⏱️ La carga anterior quedó sin terminar y se descartó por inactividad.');
        }

        $quiereEmpezar = in_array($comando, ['avance', 'cargar', 'carga', 'nuevo', 'nueva'], true)
            || ($entrada['id'] ?? '') === 'iniciar';

        if ($quiereEmpezar) {
            $sesion?->delete();
            $this->iniciar($telefono, $usuario);
            return;
        }

        if (! $sesion) {
            $this->wa->enviarBotones($telefono,
                "Hola *{$usuario->name}* 👋 Soy el sistema Taguay 🌾\n¿Querés cargar un Avance de Campo?",
                ['iniciar' => '📋 Cargar avance']);
            return;
        }

        $this->continuar($sesion, $entrada);
    }

    // =====================================================================
    //  Inicio
    // =====================================================================

    private function iniciar(string $telefono, User $usuario): void
    {
        $campania = Campania::where('activo', true)->orderByDesc('codfinneg')->first();

        if (! $campania) {
            $this->wa->enviarTexto($telefono, 'No hay una campaña activa en el sistema. Avisale al administrador.');
            return;
        }

        $sesion = WhatsAppSesion::create([
            'telefono' => $telefono,
            'user_id' => $usuario->id,
            'paso' => 'establecimiento',
            'datos' => ['campania_id' => $campania->id, 'campania' => $campania->name, 'propietario' => true],
        ]);

        $this->wa->enviarTexto($telefono, "📋 *Nuevo Avance de Campo*\nCampaña *{$campania->name}* · Fecha *" . now()->format('d/m/Y') . "*\n\nEn cualquier momento escribí *cancelar* para salir.");
        $this->preguntarEstablecimiento($sesion);
    }

    // =====================================================================
    //  Despachador: según el paso en que está, procesa la respuesta
    // =====================================================================

    private function continuar(WhatsAppSesion $s, array $e): void
    {
        $id = (string) ($e['id'] ?? '');
        $texto = trim((string) ($e['texto'] ?? ''));

        // Paginación de listas ("Ver más")
        if (Str::startsWith($id, 'mas:')) {
            [, $lista, $pagina] = explode(':', $id);
            match ($lista) {
                'est' => $this->preguntarEstablecimiento($s, (int) $pagina),
                'lote' => $this->preguntarLote($s, (int) $pagina),
                'cul' => $this->preguntarCultivoLista($s, (int) $pagina),
                default => null,
            };
            return;
        }

        switch ($s->paso) {
            case 'establecimiento':
                if (! Str::startsWith($id, 'est:')) {
                    $this->repetir($s, 'Elegí el establecimiento tocando *Ver establecimientos*.');
                    return;
                }
                $est = Establecimiento::find((int) Str::after($id, 'est:'));
                $s->guardar(['establecimiento_id' => $est->id, 'establecimiento' => $est->nombre], 'lote');
                $this->preguntarLote($s);
                return;

            case 'lote':
                if (! Str::startsWith($id, 'lote:')) {
                    $this->repetir($s, 'Elegí el lote tocando *Ver lotes*.');
                    return;
                }
                $lote = Lote::with('cultivo')->find((int) Str::after($id, 'lote:'));
                $s->guardar(['lote_id' => $lote->id, 'lote' => $lote->nombre, 'lote_ha' => $lote->hectareas]);
                $this->despuesDe($s, 'lote', fn () => $this->preguntarCultivo($s, $lote));
                return;

            case 'cultivo':
                if ($id === 'cul:otro') {
                    $this->preguntarCultivoLista($s);
                    return;
                }
                if (! Str::startsWith($id, 'cul:')) {
                    $this->repetir($s, 'Elegí el cultivo con los botones o la lista.');
                    return;
                }
                $cultivo = Cultivo::find((int) Str::after($id, 'cul:'));
                $s->guardar(['cultivo_id' => $cultivo->id, 'cultivo' => $cultivo->name]);
                $this->despuesDe($s, 'cultivo', fn () => $this->preguntarCantidad($s));
                return;

            case 'cantidad':
                $kg = $this->numero($texto);
                if ($kg === null || $kg <= 0 || $kg > 1000000000) {
                    $this->wa->enviarTexto($s->telefono, '⚠️ No entendí la cantidad. Escribí solo el número de *kilos*, por ejemplo *15020*.');
                    return;
                }
                $s->guardar(['cantidad' => $kg]);
                $this->despuesDe($s, 'cantidad', fn () => $this->preguntarSuperficie($s));
                return;

            case 'superficie':
                if ($id !== 'saltear') {
                    $ha = $this->numero($texto);
                    if ($ha === null || $ha <= 0 || $ha > 100000) {
                        $this->wa->enviarTexto($s->telefono, '⚠️ No entendí la superficie. Escribí las *hectáreas*, por ejemplo *15*, o tocá *Saltear*.');
                        return;
                    }
                    $s->guardar(['superficie' => $ha]);
                } else {
                    $s->guardar(['superficie' => null]);
                }
                $this->despuesDe($s, 'superficie', fn () => $this->preguntarCamion($s));
                return;

            case 'camion':
                if ($id === 'camion:si') {
                    $s->guardar([], 'patente');
                    $this->preguntarTextoOpcional($s, '🚛 *Patente* del camión (o tocá Saltear):');
                } else {
                    $this->despuesDe($s, 'camion', fn () => $this->preguntarAdjuntos($s));
                }
                return;

            case 'patente':
                $s->guardar(['chasis' => $this->opcional($id, $texto, 100, true)], 'chofer');
                $this->preguntarTextoOpcional($s, '👤 Nombre del *chofer* (o tocá Saltear):');
                return;

            case 'chofer':
                $s->guardar(['chofer' => $this->opcional($id, $texto, 150)], 'numero');
                $this->preguntarTextoOpcional($s, '🔢 *Número / CTG* (o tocá Saltear):');
                return;

            case 'numero':
                $s->guardar(['numero' => $this->opcional($id, $texto, 100)], 'humedad');
                $this->preguntarTextoOpcional($s, '💧 *Humedad* en % (o tocá Saltear):');
                return;

            case 'humedad':
                if ($id !== 'saltear') {
                    $hum = $this->numero($texto);
                    if ($hum === null || $hum < 0 || $hum > 100) {
                        $this->wa->enviarBotones($s->telefono, '⚠️ La humedad va de 0 a 100. Escribila de nuevo o tocá Saltear.', ['saltear' => '⏭️ Saltear']);
                        return;
                    }
                    $s->guardar(['humedad' => $hum], 'propiedad');
                } else {
                    $s->guardar(['humedad' => null], 'propiedad');
                }
                $this->wa->enviarBotones($s->telefono, '¿La carga es *propia* (Taguay) o de un *socio*?', [
                    'prop:propio' => '🏠 Propia',
                    'prop:socio' => '🤝 De socio',
                ]);
                return;

            case 'propiedad':
                if (! in_array($id, ['prop:propio', 'prop:socio'], true)) {
                    $this->wa->enviarBotones($s->telefono, 'Elegí una opción:', ['prop:propio' => '🏠 Propia', 'prop:socio' => '🤝 De socio']);
                    return;
                }
                $s->guardar(['propietario' => $id === 'prop:propio']);
                $this->despuesDe($s, 'propiedad', fn () => $this->preguntarAdjuntos($s));
                return;

            case 'adjuntos':
                $this->procesarAdjuntos($s, $e);
                return;

            case 'confirmar':
                $this->procesarConfirmacion($s, $id);
                return;

            case 'corregir':
                $this->procesarCorreccion($s, $id);
                return;

            default:
                $s->delete();
                $this->wa->enviarTexto($s->telefono, 'Escribí *avance* para empezar una carga.');
        }
    }

    // =====================================================================
    //  Preguntas
    // =====================================================================

    private function preguntarEstablecimiento(WhatsAppSesion $s, int $pagina = 1): void
    {
        $s->guardar([], 'establecimiento');

        // Solo establecimientos que tienen lotes en la campaña de la carga
        $items = Establecimiento::whereHas('lotes', fn ($q) => $q->where('campania_id', $s->dato('campania_id')))
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(fn ($e) => ["est:{$e->id}" => $e->nombre])
            ->all();

        $this->enviarListaPaginada($s, '🏡 ¿En qué *establecimiento*?', 'Ver establecimientos', $items, 'est', $pagina);
    }

    private function preguntarLote(WhatsAppSesion $s, int $pagina = 1): void
    {
        $s->guardar([], 'lote');

        $items = Lote::with('cultivo')
            ->where('establecimiento_id', $s->dato('establecimiento_id'))
            ->where('campania_id', $s->dato('campania_id'))
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(fn ($l) => ["lote:{$l->id}" => [
                'title' => $l->nombre,
                'description' => trim(($l->hectareas ? $this->formato($l->hectareas) . ' ha' : '') . ($l->cultivo ? ' · ' . $l->cultivo->name : ''), ' ·'),
            ]])
            ->all();

        if (! $items) {
            $this->wa->enviarTexto($s->telefono, "⚠️ *{$s->dato('establecimiento')}* no tiene lotes en la campaña {$s->dato('campania')}. Elegí otro establecimiento.");
            $this->preguntarEstablecimiento($s);
            return;
        }

        $this->enviarListaPaginada($s, "🌱 *{$s->dato('establecimiento')}* · ¿Qué *lote*?", 'Ver lotes', $items, 'lote', $pagina);
    }

    private function preguntarCultivo(WhatsAppSesion $s, Lote $lote): void
    {
        $s->guardar([], 'cultivo');

        // Si el lote ya tiene cultivo asignado, se ofrece con un toque
        if ($lote->cultivo) {
            $this->wa->enviarBotones($s->telefono, '🌾 ¿Qué *cultivo*?', [
                "cul:{$lote->cultivo->id}" => $lote->cultivo->name,
                'cul:otro' => 'Otro cultivo',
            ]);
            return;
        }

        $this->preguntarCultivoLista($s);
    }

    private function preguntarCultivoLista(WhatsAppSesion $s, int $pagina = 1): void
    {
        $s->guardar([], 'cultivo');

        $items = Cultivo::where('filtro_power_bi', true)->orderBy('name')->get()
            ->mapWithKeys(fn ($c) => ["cul:{$c->id}" => $c->name])
            ->all();

        $this->enviarListaPaginada($s, '🌾 ¿Qué *cultivo*?', 'Ver cultivos', $items, 'cul', $pagina);
    }

    private function preguntarCantidad(WhatsAppSesion $s): void
    {
        $s->guardar([], 'cantidad');
        $this->wa->enviarTexto($s->telefono, '⚖️ ¿Qué *cantidad* en *kilos*? Escribí solo el número, por ejemplo *15020*.');
    }

    private function preguntarSuperficie(WhatsAppSesion $s): void
    {
        $s->guardar([], 'superficie');
        $ayuda = $s->dato('lote_ha') ? " (el lote tiene {$this->formato($s->dato('lote_ha'))} ha)" : '';
        $this->wa->enviarBotones($s->telefono, "📐 ¿Qué *superficie* en hectáreas{$ayuda}? Escribí el número o tocá Saltear.", ['saltear' => '⏭️ Saltear']);
    }

    private function preguntarCamion(WhatsAppSesion $s): void
    {
        $s->guardar([], 'camion');
        $this->wa->enviarBotones($s->telefono, "🚛 ¿Querés agregar los *datos del camión*?\n(patente, chofer, número/CTG, humedad y si es propia o de socio)", [
            'camion:si' => 'Sí, agregar',
            'camion:no' => 'No',
        ]);
    }

    private function preguntarTextoOpcional(WhatsAppSesion $s, string $pregunta): void
    {
        $this->wa->enviarBotones($s->telefono, $pregunta, ['saltear' => '⏭️ Saltear']);
    }

    private function preguntarAdjuntos(WhatsAppSesion $s, ?string $aviso = null): void
    {
        $s->guardar([], 'adjuntos');

        $estado = [];
        $fotos = count($s->dato('imagenes', []));
        if ($fotos) {
            $estado[] = "📷 {$fotos} foto" . ($fotos > 1 ? 's' : '');
        }
        if ($s->dato('latitud')) {
            $estado[] = '📍 ubicación';
        }

        $texto = ($aviso ? $aviso . "\n\n" : '')
            . '¿Querés agregar una *foto* o la *ubicación*?'
            . ($estado ? "\nYa tenés: " . implode(' y ', $estado) . '.' : '')
            . "\n\nPodés mandarla directamente: la foto con 📎 › Cámara, la ubicación con 📎 › Ubicación.";

        $this->wa->enviarBotones($s->telefono, $texto, [
            'adj:foto' => '📷 Foto',
            'adj:ubicacion' => '📍 Ubicación',
            'adj:seguir' => '➡️ Seguir',
        ]);
    }

    // =====================================================================
    //  Fotos y ubicación
    // =====================================================================

    private function procesarAdjuntos(WhatsAppSesion $s, array $e): void
    {
        $id = (string) ($e['id'] ?? '');

        if (($e['tipo'] ?? '') === 'image' && ! empty($e['media_id'])) {
            $imagenes = $s->dato('imagenes', []);
            if (count($imagenes) >= 2) {
                $this->preguntarAdjuntos($s, '⚠️ Ya hay 2 fotos (el máximo).');
                return;
            }
            $ruta = $this->guardarFoto($e['media_id']);
            if (! $ruta) {
                $this->preguntarAdjuntos($s, '⚠️ No pude descargar la foto. Probá mandarla de nuevo.');
                return;
            }
            $imagenes[] = $ruta;
            $s->guardar(['imagenes' => $imagenes]);
            $this->preguntarAdjuntos($s, '✅ Foto recibida.');
            return;
        }

        if (($e['tipo'] ?? '') === 'location' && isset($e['latitud'], $e['longitud'])) {
            $s->guardar(['latitud' => (float) $e['latitud'], 'longitud' => (float) $e['longitud']]);
            $this->preguntarAdjuntos($s, '✅ Ubicación recibida.');
            return;
        }

        match ($id) {
            'adj:foto' => $this->wa->enviarTexto($s->telefono, '📷 Mandá la foto ahora: tocá 📎 › *Cámara* o *Galería*.'),
            'adj:ubicacion' => $this->wa->enviarTexto($s->telefono, '📍 Mandá tu ubicación: tocá 📎 › *Ubicación* › *Enviar tu ubicación actual*.'),
            'adj:seguir' => $this->despuesDe($s, 'adjuntos', fn () => $this->mostrarResumen($s)),
            default => $this->preguntarAdjuntos($s),
        };
    }

    /** Descarga la foto de WhatsApp y la guarda igual que las de la app. Devuelve la ruta relativa. */
    private function guardarFoto(string $mediaId): ?string
    {
        $media = $this->wa->descargarMedia($mediaId);
        if (! $media) {
            return null;
        }

        $extension = match ($media['mime']) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $nombre = 'wa_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;
        $ruta = 'lote_estados/' . $nombre;

        Storage::disk('public')->put($ruta, $media['contenido']);

        // Copia en public/storage (el hosting no tiene el enlace simbólico de Laravel)
        $dirPublico = public_path('storage/lote_estados');
        if (! File::exists($dirPublico)) {
            File::makeDirectory($dirPublico, 0755, true);
        }
        File::put($dirPublico . '/' . $nombre, $media['contenido']);

        return $ruta;
    }

    // =====================================================================
    //  Resumen, guardar y corregir
    // =====================================================================

    private function mostrarResumen(WhatsAppSesion $s): void
    {
        $s->guardar(['volver_a_resumen' => false], 'confirmar');

        $lineas = [
            '📋 *Revisá la carga:*',
            '',
            "🗓️ {$s->dato('campania')} · " . now()->format('d/m/Y') . " · {$this->nombreEstado()}",
            "🏡 {$s->dato('establecimiento')} · *{$s->dato('lote')}*",
            "🌾 {$s->dato('cultivo')}",
            '⚖️ *' . $this->formato($s->dato('cantidad'), 0) . ' kg*',
        ];

        if ($s->dato('superficie')) {
            $lineas[] = '📐 ' . $this->formato($s->dato('superficie')) . ' ha · rinde *' . $this->formato($this->rinde($s), 2, true) . ' Tn/ha*';
        }
        if ($s->dato('chasis') || $s->dato('chofer') || $s->dato('numero')) {
            $lineas[] = '🚛 ' . implode(' · ', array_filter([$s->dato('chasis'), $s->dato('chofer'), $s->dato('numero')]));
        }
        if ($s->dato('humedad') !== null) {
            $lineas[] = '💧 Humedad ' . $this->formato($s->dato('humedad')) . ' %';
        }
        $lineas[] = $s->dato('propietario', true) ? '🏠 Propia' : '🤝 De socio';
        if ($s->dato('imagenes') || $s->dato('latitud')) {
            $lineas[] = trim(($s->dato('imagenes') ? '📷 ' . count($s->dato('imagenes')) . ' foto(s) ' : '') . ($s->dato('latitud') ? '📍 con ubicación' : ''));
        }

        $this->wa->enviarBotones($s->telefono, implode("\n", $lineas), [
            'ok:guardar' => '✅ Guardar',
            'ok:corregir' => '✏️ Corregir',
            'cancelar' => '❌ Cancelar',
        ]);
    }

    private function procesarConfirmacion(WhatsAppSesion $s, string $id): void
    {
        if ($id === 'ok:corregir') {
            $s->guardar([], 'corregir');
            $this->wa->enviarLista($s->telefono, '✏️ ¿Qué querés corregir?', 'Elegir dato', [
                'cor:lote' => ['title' => 'Establecimiento y lote', 'description' => "{$s->dato('establecimiento')} · {$s->dato('lote')}"],
                'cor:cultivo' => ['title' => 'Cultivo', 'description' => (string) $s->dato('cultivo')],
                'cor:cantidad' => ['title' => 'Cantidad (kg)', 'description' => $this->formato($s->dato('cantidad'), 0) . ' kg'],
                'cor:superficie' => ['title' => 'Superficie (ha)', 'description' => $s->dato('superficie') ? $this->formato($s->dato('superficie')) . ' ha' : 'Sin dato'],
                'cor:camion' => ['title' => 'Datos del camión', 'description' => 'Patente, chofer, número, humedad, propia/socio'],
                'cor:adjuntos' => ['title' => 'Foto y ubicación'],
            ], 'Datos de la carga');
            return;
        }

        if ($id !== 'ok:guardar') {
            $this->mostrarResumen($s);
            return;
        }

        $registro = $this->guardarAvance($s);
        $s->delete();

        $this->wa->enviarTexto($s->telefono,
            "✅ *Avance de campo guardado* (N° {$registro->id}).\n" .
            "{$s->dato('establecimiento')} · {$s->dato('lote')} · " . $this->formato($s->dato('cantidad'), 0) . " kg\n\n" .
            'Escribí *avance* para cargar otro.');
    }

    private function procesarCorreccion(WhatsAppSesion $s, string $id): void
    {
        $s->guardar(['volver_a_resumen' => true]);

        match ($id) {
            'cor:lote' => $this->preguntarEstablecimiento($s),
            'cor:cultivo' => $this->preguntarCultivoLista($s),
            'cor:cantidad' => $this->preguntarCantidad($s),
            'cor:superficie' => $this->preguntarSuperficie($s),
            'cor:camion' => (function () use ($s) {
                $s->guardar([], 'patente');
                $this->preguntarTextoOpcional($s, '🚛 *Patente* del camión (o tocá Saltear):');
            })(),
            'cor:adjuntos' => $this->preguntarAdjuntos($s),
            default => $this->mostrarResumen($s),
        };
    }

    private function guardarAvance(WhatsAppSesion $s): LoteEstado
    {
        $imagenes = $s->dato('imagenes', []);
        $lat = $s->dato('latitud');
        $lng = $s->dato('longitud');

        $registro = LoteEstado::create([
            'lote_id' => $s->dato('lote_id'),
            'cultivo_id' => $s->dato('cultivo_id'),
            'estado_cultivo_id' => $this->estadoPorDefecto()?->id,
            'user_id' => $s->user_id,
            'fecha' => now()->toDateString(),
            'cantidad' => $s->dato('cantidad'),
            'superficie_ha' => $s->dato('superficie'),
            'rinde' => $this->rinde($s),
            'chasis' => $s->dato('chasis'),
            'chofer' => $s->dato('chofer'),
            'numero' => $s->dato('numero'),
            'humedad' => $s->dato('humedad'),
            'propietario' => (bool) $s->dato('propietario', true),
            'pertenece_empresa' => true,   // mismo valor por defecto que el formulario de la app
            'silo' => false,
            'imagen_1' => $imagenes[0] ?? null,
            'imagen_2' => $imagenes[1] ?? null,
            'latitud' => $lat,
            'longitud' => $lng,
            'link_google_maps' => $lat && $lng ? "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}" : null,
            'observacion' => 'Cargado por WhatsApp.',
        ]);

        Log::channel('whatsapp')->info('AVANCE GUARDADO', ['id' => $registro->id, 'telefono' => $s->telefono, 'user_id' => $s->user_id]);

        return $registro;
    }

    // =====================================================================
    //  Ayudantes
    // =====================================================================

    /**
     * Después de responder un bloque: si se estaba corrigiendo, vuelve al resumen;
     * si no, sigue con la pregunta siguiente.
     */
    private function despuesDe(WhatsAppSesion $s, string $bloque, callable $siguiente): void
    {
        if ($s->dato('volver_a_resumen')) {
            // Los bloques de varios pasos (camión) solo vuelven al terminar el último
            $finDeBloque = ['lote', 'cultivo', 'cantidad', 'superficie', 'propiedad', 'camion', 'adjuntos'];
            if (in_array($bloque, $finDeBloque, true)) {
                $this->mostrarResumen($s);
                return;
            }
        }
        $siguiente();
    }

    /** Lista con paginación: 9 opciones + "Ver más" si hay más. */
    private function enviarListaPaginada(WhatsAppSesion $s, string $texto, string $boton, array $items, string $prefijo, int $pagina): void
    {
        $total = count($items);
        $paginas = max(1, (int) ceil($total / self::POR_PAGINA));
        $pagina = min(max(1, $pagina), $paginas);

        $opciones = array_slice($items, ($pagina - 1) * self::POR_PAGINA, self::POR_PAGINA, true);
        if ($pagina < $paginas) {
            $opciones["mas:{$prefijo}:" . ($pagina + 1)] = ['title' => '➡️ Ver más', 'description' => "Página " . ($pagina + 1) . " de {$paginas}"];
        }

        $this->wa->enviarLista($s->telefono, $texto . ($paginas > 1 ? "\n_(página {$pagina} de {$paginas})_" : ''), $boton, $opciones);
    }

    private function repetir(WhatsAppSesion $s, string $aviso): void
    {
        $this->wa->enviarTexto($s->telefono, '👉 ' . $aviso);
        match ($s->paso) {
            'establecimiento' => $this->preguntarEstablecimiento($s),
            'lote' => $this->preguntarLote($s),
            default => null,
        };
    }

    /** Texto libre opcional: "Saltear" → null. La patente se guarda en mayúsculas y sin espacios. */
    private function opcional(string $id, string $texto, int $max, bool $esPatente = false): ?string
    {
        if ($id === 'saltear' || $texto === '' || $this->normalizar($texto) === 'saltear') {
            return null;
        }
        $valor = $esPatente ? strtoupper(preg_replace('/\s+/', '', $texto)) : $texto;
        return mb_substr($valor, 0, $max);
    }

    /**
     * Interpreta números escritos como en Argentina:
     * "15020", "15.020" y "15.020,5" (punto de miles); "15,5" y "15.5" (decimales).
     */
    private function numero(string $texto): ?float
    {
        $t = str_replace([' ', 'kg', 'KG', 'Kg', 'ha', 'HA', 'Ha', '%'], '', trim($texto));

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $t)) {
            return (float) str_replace(['.', ','], ['', '.'], $t);
        }
        if (preg_match('/^\d+(,\d+)?$/', $t)) {
            return (float) str_replace(',', '.', $t);
        }
        if (preg_match('/^\d+(\.\d+)?$/', $t)) {
            return (float) $t;
        }

        return null;
    }

    private function rinde(WhatsAppSesion $s): ?float
    {
        $kg = $s->dato('cantidad');
        $ha = $s->dato('superficie');
        return $kg && $ha ? round(($kg / 1000) / $ha, 2) : null;
    }

    private function estadoPorDefecto(): ?EstadoCultivo
    {
        return EstadoCultivo::where('activo', true)->where('por_defecto', true)->first()
            ?? EstadoCultivo::where('nombre', 'Cosecha')->first();
    }

    private function nombreEstado(): string
    {
        return $this->estadoPorDefecto()?->nombre ?? 'Sin estado';
    }

    /**
     * Número con formato argentino. Los enteros se muestran sin decimales (15.020 / 45),
     * salvo que se pidan decimales fijos (rinde: 1,00).
     */
    private function formato($valor, int $decimales = 2, bool $fijos = false): string
    {
        $valor = (float) $valor;
        if (! $fijos && floor($valor) == $valor) {
            $decimales = 0;
        }
        return number_format($valor, $decimales, ',', '.');
    }

    /** Minúsculas y sin tildes, para reconocer comandos escritos de distintas formas. */
    private function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii(trim($texto)));
    }
}
