<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CultivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // ajustable
    }

    // Vista única
    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $cultivoEdit = $editId ? Cultivo::find($editId) : null;

        $cultivos = Cultivo::orderBy('name')->get();

        return view('abm.cultivos.index', compact('cultivos', 'cultivoEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'codfinneg' => ['required','string','max:30','unique:cultivos,codfinneg'],
            'filtro_power_bi' => ['nullable'], // checkbox
        ]);

        Cultivo::create([
            'name' => trim($data['name']),
            'codfinneg' => strtoupper(trim($data['codfinneg'])),
            'filtro_power_bi' => $request->boolean('filtro_power_bi'),
        ]);

        return redirect()->route('cultivos.index')->with('success', 'Cultivo creado.');
    }

    public function update(Request $request, Cultivo $cultivo)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'codfinneg' => ['required','string','max:30', Rule::unique('cultivos','codfinneg')->ignore($cultivo->id)],
            'filtro_power_bi' => ['nullable'],
        ]);

        $cultivo->update([
            'name' => trim($data['name']),
            'codfinneg' => strtoupper(trim($data['codfinneg'])),
            'filtro_power_bi' => $request->boolean('filtro_power_bi'),
        ]);

        return redirect()->route('cultivos.index')->with('success', 'Cultivo actualizado.');
    }

    public function destroy(Cultivo $cultivo)
    {
        $cultivo->delete();

        return redirect()->route('cultivos.index')->with('success', 'Cultivo eliminado.');
    }
}
