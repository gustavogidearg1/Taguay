<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Rinde;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RindeController extends Controller
{
    public function index(Request $request)
    {
        $loteId = $request->get('lote_id');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $buscar = trim((string) $request->get('buscar'));

        $registros = Rinde::with(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'user'])
            ->when($loteId, fn($q) => $q->where('lote_id', $loteId))
            ->when($fechaDesde, fn($q) => $q->whereDate('fecha', '>=', $fechaDesde))
            ->when($fechaHasta, fn($q) => $q->whereDate('fecha', '<=', $fechaHasta))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('observacion', 'like', "%{$buscar}%")
                        ->orWhereHas('lote', function ($l) use ($buscar) {
                            $l->where('nombre', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote.establecimiento', function ($e) use ($buscar) {
                            $e->where('nombre', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote.cultivo', function ($c) use ($buscar) {
                            $c->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote.campania', function ($ca) use ($buscar) {
                            $ca->where('name', 'like', "%{$buscar}%");
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

        return view('abm.rindes.index', compact(
            'registros',
            'lotes',
            'loteId',
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

        return view('abm.rindes.create', compact('lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => ['required', 'exists:lotes,id'],
            'fecha' => ['required', 'date'],
            'rinde' => ['required', 'numeric', 'min:0'],
            'humedad' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'superficie_cosechada' => ['nullable', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string'],
        ]);

        $data['user_id'] = Auth::id();

        Rinde::create($data);

        return redirect()
            ->route('rindes.index')
            ->with('success', 'Rinde registrado correctamente.');
    }

    public function show(Rinde $rinde)
    {
        $rinde->load(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'user']);

        return view('abm.rindes.show', compact('rinde'));
    }

    public function edit(Rinde $rinde)
    {
        $lotes = Lote::with(['establecimiento', 'campania', 'cultivo'])
            ->orderBy('nombre')
            ->get();

        return view('abm.rindes.edit', compact('rinde', 'lotes'));
    }

    public function update(Request $request, Rinde $rinde)
    {
        $data = $request->validate([
            'lote_id' => ['required', 'exists:lotes,id'],
            'fecha' => ['required', 'date'],
            'rinde' => ['required', 'numeric', 'min:0'],
            'humedad' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'superficie_cosechada' => ['nullable', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string'],
        ]);

        $rinde->update($data);

        return redirect()
            ->route('rindes.index')
            ->with('success', 'Rinde actualizado correctamente.');
    }

    public function destroy(Rinde $rinde)
    {
        $rinde->delete();

        return redirect()
            ->route('rindes.index')
            ->with('success', 'Rinde eliminado correctamente.');
    }
}
