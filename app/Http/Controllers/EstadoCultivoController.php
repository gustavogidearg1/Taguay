<?php

namespace App\Http\Controllers;

use App\Models\EstadoCultivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'por_defecto' => ['nullable', 'boolean'],
        ]);

        $data = $this->normalizarFlags($request, $data);

        DB::transaction(function () use ($data) {
            $estado = EstadoCultivo::create($data);

            if ($estado->por_defecto) {
                $estado->marcarComoUnicoPorDefecto();
            }
        });

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
            'por_defecto' => ['nullable', 'boolean'],
        ]);

        $data = $this->normalizarFlags($request, $data);

        DB::transaction(function () use ($estados_cultivo, $data) {
            $estados_cultivo->update($data);

            if ($estados_cultivo->por_defecto) {
                $estados_cultivo->marcarComoUnicoPorDefecto();
            }
        });

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

    /**
     * Convierte los checkboxes en booleanos.
     * Un estado por defecto siempre debe estar activo (si no, no aparecería en la Carga Rápida).
     */
    private function normalizarFlags(Request $request, array $data): array
    {
        $data['por_defecto'] = $request->boolean('por_defecto');
        $data['activo'] = $request->boolean('activo') || $data['por_defecto'];

        return $data;
    }
}
