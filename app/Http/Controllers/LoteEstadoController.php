<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteEstado;
use App\Models\EstadoCultivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoteEstadoController extends Controller
{
    public function index(Request $request)
    {
        $loteId = $request->get('lote_id');
        $estadoId = $request->get('estado_cultivo_id');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $buscar = trim((string) $request->get('buscar'));

        $registros = LoteEstado::with(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'estadoCultivo', 'user'])
            ->when($loteId, fn($q) => $q->where('lote_id', $loteId))
            ->when($estadoId, fn($q) => $q->where('estado_cultivo_id', $estadoId))
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

        $estados = EstadoCultivo::where('activo', true)->orderBy('nombre')->get();

        return view('abm.lote_estados.index', compact(
            'registros',
            'lotes',
            'estados',
            'loteId',
            'estadoId',
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

        $estados = EstadoCultivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('abm.lote_estados.create', compact('lotes', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => ['required', 'exists:lotes,id'],
            'estado_cultivo_id' => ['required', 'exists:estados_cultivo,id'],
            'fecha' => ['required', 'date'],
            'observacion' => ['nullable', 'string'],
        ]);

        $data['user_id'] = Auth::id();

        LoteEstado::create($data);

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Estado del lote registrado correctamente.');
    }

    public function show(LoteEstado $lote_estado)
    {
        $lote_estado->load(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'estadoCultivo', 'user']);

        return view('abm.lote_estados.show', [
            'registro' => $lote_estado
        ]);
    }

    public function edit(LoteEstado $lote_estado)
    {
        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        $estados = EstadoCultivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('abm.lote_estados.edit', [
            'registro' => $lote_estado,
            'lotes' => $lotes,
            'estados' => $estados,
        ]);
    }

    public function update(Request $request, LoteEstado $lote_estado)
    {
        $data = $request->validate([
            'lote_id' => ['required', 'exists:lotes,id'],
            'estado_cultivo_id' => ['required', 'exists:estados_cultivo,id'],
            'fecha' => ['required', 'date'],
            'observacion' => ['nullable', 'string'],
        ]);

        $lote_estado->update($data);

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Estado del lote actualizado correctamente.');
    }

    public function destroy(LoteEstado $lote_estado)
    {
        $lote_estado->delete();

        return redirect()
            ->route('lote-estados.index')
            ->with('success', 'Estado del lote eliminado correctamente.');
    }
}
