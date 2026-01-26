<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Ajustá según tu proyecto (dejé igual que venías usando)
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $unidadEdit = $editId ? Unidad::find($editId) : null;

        $unidades = Unidad::orderBy('id', 'desc')->get();

        return view('abm.unidades.index', compact('unidades', 'unidadEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:100'],
            'codigo' => ['required','string','max:50','unique:unidades,codigo'],
            'corta'  => ['required','string','max:50'],
        ]);

        Unidad::create([
            'name'   => trim($data['name']),
            'codigo' => trim($data['codigo']),
            'corta'  => trim($data['corta']),
        ]);

        return redirect()->route('unidades.index')->with('success', 'Unidad creada.');
    }

    public function update(Request $request, Unidad $unidade) // Laravel puede inyectar como $unidade
    {
        // Para evitar confusiones con "unidade", renombro a $unidad:
        $unidad = $unidade;

        $data = $request->validate([
            'name'   => ['required','string','max:100'],
            'codigo' => ['required','string','max:50', Rule::unique('unidades','codigo')->ignore($unidad->id)],
            'corta'  => ['required','string','max:50'],
        ]);

        $unidad->update([
            'name'   => trim($data['name']),
            'codigo' => trim($data['codigo']),
            'corta'  => trim($data['corta']),
        ]);

        return redirect()->route('unidades.index')->with('success', 'Unidad actualizada.');
    }

    public function destroy(Unidad $unidade)
    {
        $unidad = $unidade;
        $unidad->delete();

        return redirect()->route('unidades.index')->with('success', 'Unidad eliminada.');
    }
}
