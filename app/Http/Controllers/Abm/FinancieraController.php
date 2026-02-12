<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\Financiera;
use Illuminate\Http\Request;

class FinancieraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $financieraEdit = $editId ? Financiera::find($editId) : null;

        $financieras = Financiera::orderBy('id', 'desc')->get();

        return view('abm.financieras.index', compact('financieras', 'financieraEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'tipo'        => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string', 'max:100'],
        ]);

        Financiera::create([
            'name'        => trim($data['name']),
            'tipo'        => isset($data['tipo']) ? trim($data['tipo']) : null,
            'descripcion' => isset($data['descripcion']) ? trim($data['descripcion']) : null,
        ]);

        return redirect()->route('financieras.index')->with('success', 'Financiera creada.');
    }

    public function update(Request $request, Financiera $financiera)
{
    $data = $request->validate([
        'name'        => ['required', 'string', 'max:100'],
        'tipo'        => ['nullable', 'string', 'max:50'],
        'descripcion' => ['nullable', 'string', 'max:100'],
    ]);

    $financiera->update([
        'name'        => trim($data['name']),
        'tipo'        => isset($data['tipo']) ? trim($data['tipo']) : null,
        'descripcion' => isset($data['descripcion']) ? trim($data['descripcion']) : null,
    ]);

    return redirect()->route('financieras.index')->with('success', 'Financiera actualizada.');
}
    public function destroy(Financiera $financiera)
    {
        $financiera->delete();

        return redirect()->route('financieras.index')->with('success', 'Financiera eliminada.');
    }

    // (No los usamos, pero resource los define)
    public function create()
    {
        return redirect()->route('financieras.index');
    }
    public function edit(Financiera $financiera)
    {

        return redirect()->route('financieras.index', ['edit' => $financiera->id]);
    }
    public function show(Financiera $financiera)
    {
        return redirect()->route('financieras.index');
    }
}
