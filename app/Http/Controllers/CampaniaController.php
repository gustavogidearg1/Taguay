<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaniaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $campaniaEdit = $editId ? Campania::find($editId) : null;

        $campanias = Campania::orderBy('name', 'desc')->get();

        return view('abm.campanias.index', compact('campanias', 'campaniaEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:20'],
            'codfinneg' => ['required','string','max:10','unique:campanias,codfinneg'],
            'activo' => ['nullable'], // checkbox
        ]);

        Campania::create([
            'name' => trim($data['name']),
            'codfinneg' => trim($data['codfinneg']),
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->route('campanias.index')->with('success', 'Campaña creada.');
    }

    public function update(Request $request, Campania $campania)
    {
        $data = $request->validate([
            'name' => ['required','string','max:20'],
            'codfinneg' => ['required','string','max:10', Rule::unique('campanias','codfinneg')->ignore($campania->id)],
            'activo' => ['nullable'],
        ]);

        $campania->update([
            'name' => trim($data['name']),
            'codfinneg' => trim($data['codfinneg']),
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->route('campanias.index')->with('success', 'Campaña actualizada.');
    }

    public function destroy(Campania $campania)
    {
        $campania->delete();
        return redirect()->route('campanias.index')->with('success', 'Campaña eliminada.');
    }
}
