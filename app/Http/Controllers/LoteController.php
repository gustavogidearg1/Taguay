<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Campania;
use App\Models\Cultivo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $campaniaId = $request->get('campania_id');
        $establecimientoId = $request->get('establecimiento_id');
        $cultivoId = $request->get('cultivo_id');
        $buscar = trim((string) $request->get('buscar'));

        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->when($campaniaId, fn($q) => $q->where('campania_id', $campaniaId))
            ->when($establecimientoId, fn($q) => $q->where('establecimiento_id', $establecimientoId))
            ->when($cultivoId, fn($q) => $q->where('cultivo_id', $cultivoId))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('ubicacion_referencia', 'like', "%{$buscar}%")
                        ->orWhereHas('establecimiento', function ($e) use ($buscar) {
                            $e->where('nombre', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('cultivo', function ($c) use ($buscar) {
                            $c->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('campania', function ($ca) use ($buscar) {
                            $ca->where('name', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $campanias = Campania::orderByDesc('name')->get();
        $establecimientos = Establecimiento::orderBy('nombre')->get();
        $cultivos = Cultivo::orderBy('name')->get();

        return view('abm.lotes.index', compact(
            'lotes',
            'campanias',
            'establecimientos',
            'cultivos',
            'campaniaId',
            'establecimientoId',
            'cultivoId',
            'buscar'
        ));
    }

    public function create()
    {
        $campanias = Campania::orderByDesc('name')->get();
        $establecimientos = Establecimiento::orderBy('nombre')->get();
        $cultivos = Cultivo::orderBy('name')->get();

        return view('abm.lotes.create', compact('campanias', 'establecimientos', 'cultivos'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if (!empty($data['latitud']) && !empty($data['longitud']) && empty($data['link_google_maps'])) {
            $data['link_google_maps'] = "https://www.google.com/maps/search/?api=1&query={$data['latitud']},{$data['longitud']}";
        }

        Lote::create($data);

        return redirect()
            ->route('lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }

    public function show(Lote $lote)
    {
        $lote->load(['establecimiento', 'campania', 'cultivo']);

        return view('abm.lotes.show', compact('lote'));
    }

    public function edit(Lote $lote)
    {
        $campanias = Campania::orderByDesc('name')->get();
        $establecimientos = Establecimiento::orderBy('nombre')->get();
        $cultivos = Cultivo::orderBy('name')->get();

        return view('abm.lotes.edit', compact('lote', 'campanias', 'establecimientos', 'cultivos'));
    }

    public function update(Request $request, Lote $lote)
    {
        $data = $this->validateData($request, $lote->id);

        if (!empty($data['latitud']) && !empty($data['longitud']) && empty($data['link_google_maps'])) {
            $data['link_google_maps'] = "https://www.google.com/maps/search/?api=1&query={$data['latitud']},{$data['longitud']}";
        }

        $lote->update($data);

        return redirect()
            ->route('lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(Lote $lote)
    {
        $lote->delete();

        return redirect()
            ->route('lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }

    private function validateData(Request $request, ?int $loteId = null): array
    {
        return $request->validate([
            'establecimiento_id' => ['required', 'exists:establecimientos,id'],
            'campania_id' => ['required', 'exists:campanias,id'],
            'cultivo_id' => ['required', 'exists:cultivos,id'],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('lotes')
                    ->where(fn($q) => $q->where('campania_id', $request->campania_id))
                    ->ignore($loteId),
            ],
            'hectareas' => ['required', 'numeric', 'min:0.01'],
            'ubicacion_referencia' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'link_google_maps' => ['nullable', 'url', 'max:500'],
        ], [
            'nombre.unique' => 'Ya existe un lote con ese nombre en la campaña seleccionada.',
        ]);
    }
}
