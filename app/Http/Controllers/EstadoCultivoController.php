<?php

namespace App\Http\Controllers;

use App\Models\EstadoCultivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstadoCultivoController extends Controller
{
    public function index()
    {
        $estados = EstadoCultivo::orderBy('nombre')->paginate(15);

        return view('abm.estados_cultivo.index', compact('estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:estados_cultivo,nombre'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        EstadoCultivo::create($data);

        return redirect()
            ->route('estados-cultivo.index')
            ->with('success', 'Estado de cultivo creado correctamente.');
    }

    public function update(Request $request, EstadoCultivo $estados_cultivo)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('estados_cultivo', 'nombre')->ignore($estados_cultivo->id),
            ],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $estados_cultivo->update($data);

        return redirect()
            ->route('estados-cultivo.index')
            ->with('success', 'Estado de cultivo actualizado correctamente.');
    }

    public function destroy(EstadoCultivo $estados_cultivo)
    {
        $estados_cultivo->delete();

        return redirect()
            ->route('estados-cultivo.index')
            ->with('success', 'Estado de cultivo eliminado correctamente.');
    }
}
