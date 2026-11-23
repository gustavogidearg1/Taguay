<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteEstado;
use App\Models\EstadoCultivo;
use App\Models\Establecimiento;
use App\Models\Campania;
use App\Models\Cultivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LoteEstadoController extends Controller
{
    public function index(Request $request)
    {
        $loteId = $request->get('lote_id');
        $estadoId = $request->get('estado_cultivo_id');
        $establecimientoId = $request->get('establecimiento_id');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $buscar = trim((string) $request->get('buscar'));

        $registros = LoteEstado::with(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'cultivo', 'estadoCultivo', 'user'])
            // El admin ve todos los movimientos; el resto, solo los propios
            ->visiblesPara(Auth::user())
            ->when($loteId, fn($q) => $q->where('lote_id', $loteId))
            ->when($estadoId, fn($q) => $q->where('estado_cultivo_id', $estadoId))
            ->when($establecimientoId, function ($q) use ($establecimientoId) {
                $q->whereHas('lote', function ($sub) use ($establecimientoId) {
                    $sub->where('establecimiento_id', $establecimientoId);
                });
            })
            ->when($fechaDesde, fn($q) => $q->whereDate('fecha', '>=', $fechaDesde))
            ->when($fechaHasta, fn($q) => $q->whereDate('fecha', '<=', $fechaHasta))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('observacion', 'like', "%{$buscar}%")
                        ->orWhereHas('lote', function ($l) use ($buscar) {
                            $l->where('nombre', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('estadoCultivo', function ($e) use ($buscar) {
                            $e->where('nombre', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote.establecimiento', function ($e) use ($buscar) {
                            $e->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        $establecimientos = Establecimiento::orderBy('nombre')->get();
        $estados = EstadoCultivo::where('activo', true)->orderBy('nombre')->get();

        return view('abm.lote_estados.index', compact(
            'registros',
            'lotes',
            'estados',
            'establecimientos',
            'loteId',
            'estadoId',
            'establecimientoId',
            'fechaDesde',
            'fechaHasta',
            'buscar'
        ));
    }

    public function create()
    {
        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        $establecimientos = Establecimiento::orderBy('nombre')->get();

        $estados = EstadoCultivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $cultivos = $this->cultivosParaSeleccion();

        return view('abm.lote_estados.create', compact('lotes', 'estados', 'establecimientos', 'cultivos'));
    }

    public function store(Request $request)
    {
        $data = $this->validateAndPrepareData($request);

        LoteEstado::create($data);

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Avance de campo registrado correctamente.');
    }

    public function show(LoteEstado $lote_estado)
    {
        $this->autorizarRegistro($lote_estado);

        $lote_estado->load(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'cultivo', 'estadoCultivo', 'user']);

        return view('abm.lote_estados.show', [
            'registro' => $lote_estado
        ]);
    }

    public function edit(LoteEstado $lote_estado)
    {
        $this->autorizarRegistro($lote_estado);

        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        $establecimientos = Establecimiento::orderBy('nombre')->get();

        $estados = EstadoCultivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('abm.lote_estados.edit', [
            'registro' => $lote_estado,
            'lotes' => $lotes,
            'estados' => $estados,
            'establecimientos' => $establecimientos,
            'cultivos' => $this->cultivosParaSeleccion($lote_estado->cultivo_id),
        ]);
    }

    public function update(Request $request, LoteEstado $lote_estado)
    {
        $this->autorizarRegistro($lote_estado);

        $data = $this->validateAndPrepareData($request, $lote_estado);

        $lote_estado->update($data);

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Avance de campo actualizado correctamente.');
    }

    public function destroy(LoteEstado $lote_estado)
    {
        // Eliminar queda reservado al administrador
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Solo el administrador puede eliminar registros.');

        if ($lote_estado->imagen_1) {
            Storage::disk('public')->delete($lote_estado->imagen_1);
        }

        if ($lote_estado->imagen_2) {
            Storage::disk('public')->delete($lote_estado->imagen_2);
        }

        $lote_estado->delete();

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Avance de campo eliminado correctamente.');
    }

    public function quickCreate()
    {
        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        $establecimientos = Establecimiento::orderBy('nombre')->get();

        $estados = EstadoCultivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $campanias = Campania::where('activo', true)
            ->orderByDesc('codfinneg')
            ->get();

        // Por defecto se preselecciona la campaña activa más reciente (hoy: 26/27).
        $campaniaDefaultId = old('campania_id', $campanias->first()->id ?? null);

        // Estado preseleccionado: el marcado como "por defecto" en el ABM de Estados de Cultivo.
        $estadoDefaultId = old('estado_cultivo_id', $estados->firstWhere('por_defecto', true)?->id);

        $cultivos = $this->cultivosParaSeleccion();

        return view('abm.lote_estados.quick_create', compact(
            'lotes', 'estados', 'establecimientos', 'campanias', 'campaniaDefaultId', 'estadoDefaultId', 'cultivos'
        ));
    }

    public function quickStore(Request $request)
    {
        $data = $this->validateAndPrepareData($request);

        LoteEstado::create($data);

return redirect()
    ->route('lote-estados.index')
    ->with('success', 'Avance de campo guardado correctamente.');
    }

    private function validateAndPrepareData(Request $request, ?LoteEstado $loteEstado = null): array
    {
        $data = $request->validate([
            'establecimiento_id' => ['required', 'exists:establecimientos,id'],
            'lote_id' => ['required', 'exists:lotes,id'],
            'estado_cultivo_id' => ['required', 'exists:estados_cultivo,id'],
            'cultivo_id' => ['required', 'exists:cultivos,id'],
            'fecha' => ['required', 'date'],
            'observacion' => ['nullable', 'string'],

            'cantidad' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],   // kilogramos
            'superficie_ha' => ['nullable', 'numeric', 'min:0'],
            'rinde' => ['nullable', 'numeric', 'min:0'],

            'imagen_1' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'imagen_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'chasis' => ['nullable', 'string', 'max:100'],
            'chofer' => ['nullable', 'string', 'max:150'],
            'pertenece_empresa' => ['nullable', 'boolean'],
            'propietario' => ['nullable', 'boolean'],

            'lluvia' => ['nullable', 'numeric', 'min:0'],
            'humedad' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'silo' => ['nullable', 'boolean'],
            'numero' => ['nullable', 'string', 'max:100'],

            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'link_google_maps' => ['nullable', 'url', 'max:500'],
        ], [
            'cultivo_id.required' => 'Elegí el cultivo.',
            'cultivo_id.exists'   => 'El cultivo elegido no existe.',
        ]);

        $lote = Lote::findOrFail($data['lote_id']);

        if ((int) $lote->establecimiento_id !== (int) $data['establecimiento_id']) {
            abort(422, 'El lote seleccionado no pertenece al establecimiento indicado.');
        }

        unset($data['establecimiento_id']);

        // El usuario que cargó el registro se guarda solo al crearlo:
        // si el admin lo edita después, el registro sigue siendo de quien lo cargó.
        if (! $loteEstado) {
            $data['user_id'] = Auth::id();
        }
        $data['pertenece_empresa'] = $request->boolean('pertenece_empresa');
        // Tildado = propio (Taguay); destildado = de socio
        $data['propietario'] = $request->boolean('propietario');
        $data['silo'] = $request->boolean('silo');

        if (!empty($data['latitud']) && !empty($data['longitud']) && empty($data['link_google_maps'])) {
            $data['link_google_maps'] = "https://www.google.com/maps/search/?api=1&query={$data['latitud']},{$data['longitud']}";
        }

if ($request->hasFile('imagen_1')) {
    if ($loteEstado && $loteEstado->imagen_1) {
        Storage::disk('public')->delete($loteEstado->imagen_1);

        $oldPublicPath = public_path('storage/' . $loteEstado->imagen_1);
        if (file_exists($oldPublicPath)) {
            @unlink($oldPublicPath);
        }
    }

    $file = $request->file('imagen_1');
    $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
    $relativePath = 'lote_estados/' . $filename;

    // Guarda en storage/app/public/lote_estados
    Storage::disk('public')->putFileAs('lote_estados', $file, $filename);

    // Copia también a public/storage/lote_estados
    $publicDir = public_path('storage/lote_estados');
    if (!File::exists($publicDir)) {
        File::makeDirectory($publicDir, 0755, true);
    }
    File::copy(
        $file->getRealPath(),
        $publicDir . DIRECTORY_SEPARATOR . $filename
    );

    $data['imagen_1'] = $relativePath;
}

if ($request->hasFile('imagen_2')) {
    if ($loteEstado && $loteEstado->imagen_2) {
        Storage::disk('public')->delete($loteEstado->imagen_2);

        $oldPublicPath = public_path('storage/' . $loteEstado->imagen_2);
        if (file_exists($oldPublicPath)) {
            @unlink($oldPublicPath);
        }
    }

    $file = $request->file('imagen_2');
    $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
    $relativePath = 'lote_estados/' . $filename;

    // Guarda en storage/app/public/lote_estados
    Storage::disk('public')->putFileAs('lote_estados', $file, $filename);

    // Copia también a public/storage/lote_estados
    $publicDir = public_path('storage/lote_estados');
    if (!File::exists($publicDir)) {
        File::makeDirectory($publicDir, 0755, true);
    }
    File::copy(
        $file->getRealPath(),
        $publicDir . DIRECTORY_SEPARATOR . $filename
    );

    $data['imagen_2'] = $relativePath;
}

        return $data;
    }

    /**
     * Cultivos para la lista desplegable: solo los marcados "Filtro Power BI = Sí" en el ABM de Cultivos.
     * Al editar se incluye también el cultivo ya guardado, aunque después se haya desmarcado.
     */
    private function cultivosParaSeleccion(?int $incluirId = null)
    {
        return Cultivo::query()
            ->where('filtro_power_bi', true)
            ->when($incluirId, fn ($q) => $q->orWhere('id', $incluirId))
            ->orderBy('name')
            ->get();
    }

    /** Un usuario no admin solo puede ver/editar sus propios registros. */
    private function autorizarRegistro(LoteEstado $loteEstado): void
    {
        abort_unless($loteEstado->esEditablePor(Auth::user()), 403, 'Solo podés ver o editar tus propios registros.');
    }
}
