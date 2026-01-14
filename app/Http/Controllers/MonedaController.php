<?php

namespace App\Http\Controllers;

use App\Models\Moneda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MonedaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // ajustalo si querés otro rol/permiso
    }

    // Vista única: listado + alta + edición en la misma pantalla
    public function index(Request $request)
    {
        $editId = $request->query('edit'); // ?edit=ID para mostrar el form de edición
        $monedaEdit = null;

        if ($editId) {
            $monedaEdit = Moneda::find($editId);
        }

        $monedas = Moneda::orderBy('name')->get();

        return view('abm.monedas.index', compact('monedas', 'monedaEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'codfinne' => ['required','string','max:20','unique:monedas,codfinne'],
        ]);

        Moneda::create([
            'name' => trim($data['name']),
            'codfinne' => strtoupper(trim($data['codfinne'])),
        ]);

        return redirect()->route('monedas.index')->with('success', 'Moneda creada.');
    }

    public function update(Request $request, Moneda $moneda)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'codfinne' => ['required','string','max:20', Rule::unique('monedas','codfinne')->ignore($moneda->id)],
        ]);

        $moneda->update([
            'name' => trim($data['name']),
            'codfinne' => strtoupper(trim($data['codfinne'])),
        ]);

        return redirect()->route('monedas.index')->with('success', 'Moneda actualizada.');
    }

    public function destroy(Moneda $moneda)
    {
        $moneda->delete();

        return redirect()->route('monedas.index')->with('success', 'Moneda eliminada.');
    }
}
