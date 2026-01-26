<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\TipoProducto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Ajustá a tus roles:
        // - si es Taguay: role:admin
        // - si es MisPuntos: role:admin_sitio|admin_empresa
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $editId = $request->query('edit');
        $tipoEdit = $editId ? TipoProducto::find($editId) : null;

        $tipos = TipoProducto::orderBy('id', 'desc')->get();

        return view('abm.tipo_productos.index', compact('tipos', 'tipoEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:100'],
            'codigo' => ['required','string','max:50','unique:tipo_productos,codigo'],
        ]);

        TipoProducto::create([
            'name'   => trim($data['name']),
            'codigo' => trim($data['codigo']),
        ]);

        return redirect()->route('tipo-productos.index')->with('success', 'Tipo de producto creado.');
    }

    public function update(Request $request, TipoProducto $tipoProducto)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:100'],
            'codigo' => ['required','string','max:50', Rule::unique('tipo_productos','codigo')->ignore($tipoProducto->id)],
        ]);

        $tipoProducto->update([
            'name'   => trim($data['name']),
            'codigo' => trim($data['codigo']),
        ]);

        return redirect()->route('tipo-productos.index')->with('success', 'Tipo de producto actualizado.');
    }

    public function destroy(TipoProducto $tipoProducto)
    {
        $tipoProducto->delete();
        return redirect()->route('tipo-productos.index')->with('success', 'Tipo de producto eliminado.');
    }
}
