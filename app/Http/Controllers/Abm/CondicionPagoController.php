<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\CondicionPago;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CondicionPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // ajustá si usás otro rol
    }

    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $condicionEdit = $editId ? CondicionPago::find($editId) : null;

        $condiciones = CondicionPago::orderBy('id', 'desc')->get();

        return view('abm.condicion_pagos.index', compact('condiciones', 'condicionEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'codigo'   => ['required','string','max:50','unique:condicion_pagos,codigo'],
            'div_mes'  => ['nullable','integer','min:0'],
            'num_dias' => ['nullable','integer','min:0'],
            'activo'   => ['nullable'],
        ]);

        CondicionPago::create([
            'name'     => trim($data['name']),
            'codigo'   => trim($data['codigo']),
            'div_mes'  => $data['div_mes'] ?? null,
            'num_dias' => $data['num_dias'] ?? null,
            'activo'   => $request->boolean('activo', true),
        ]);

        return redirect()->route('condicion-pagos.index')->with('success', 'Condición de pago creada.');
    }

    public function update(Request $request, CondicionPago $condicionPago)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'codigo'   => ['required','string','max:50', Rule::unique('condicion_pagos','codigo')->ignore($condicionPago->id)],
            'div_mes'  => ['nullable','integer','min:0'],
            'num_dias' => ['nullable','integer','min:0'],
            'activo'   => ['nullable'],
        ]);

        $condicionPago->update([
            'name'     => trim($data['name']),
            'codigo'   => trim($data['codigo']),
            'div_mes'  => $data['div_mes'] ?? null,
            'num_dias' => $data['num_dias'] ?? null,
            'activo'   => $request->boolean('activo'),
        ]);

        return redirect()->route('condicion-pagos.index')->with('success', 'Condición de pago actualizada.');
    }

    public function destroy(CondicionPago $condicionPago)
    {
        $condicionPago->delete();
        return redirect()->route('condicion-pagos.index')->with('success', 'Condición de pago eliminada.');
    }
}
