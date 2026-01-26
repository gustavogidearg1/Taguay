<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Unidad;
use App\Models\TipoProducto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // ajustá si usás otro rol
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $productos = Producto::with(['unidad','tipoProducto'])
            ->when($q !== '', function($query) use ($q) {
                $query->where(function($qq) use ($q){
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('codigo', 'like', "%{$q}%");
                });
            })
            ->orderBy('id','desc')
            ->paginate(15)
            ->withQueryString();

        return view('abm.productos.index', compact('productos','q'));
    }

    public function create()
    {
        $unidades = Unidad::orderBy('name')->get();
        $tipos    = TipoProducto::orderBy('name')->get();

        return view('abm.productos.create', compact('unidades','tipos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required','string','max:100'],
            'codigo'           => ['required','string','max:50','unique:productos,codigo'],
            'unidad_id'        => ['required','integer','exists:unidades,id'],
            'tipo_producto_id' => ['required','integer','exists:tipo_productos,id'],

            'activo' => ['nullable'],
            'stock'  => ['nullable'],
            'vende'  => ['nullable'],

            'minimo' => ['nullable','numeric','min:0'],
            'maximo' => ['nullable','numeric','min:0'],
            'obser'  => ['nullable','string','max:200'],
        ]);

        Producto::create([
            'name'             => trim($data['name']),
            'codigo'           => trim($data['codigo']),
            'unidad_id'        => $data['unidad_id'],
            'tipo_producto_id' => $data['tipo_producto_id'],

            'activo' => $request->boolean('activo', true),
            'stock'  => $request->boolean('stock', true),
            'vende'  => $request->boolean('vende', true),

            'minimo' => $data['minimo'] ?? null,
            'maximo' => $data['maximo'] ?? null,
            'obser'  => isset($data['obser']) ? trim($data['obser']) : null,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['unidad','tipoProducto']);
        return view('abm.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $unidades = Unidad::orderBy('name')->get();
        $tipos    = TipoProducto::orderBy('name')->get();

        return view('abm.productos.edit', compact('producto','unidades','tipos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'name'             => ['required','string','max:100'],
            'codigo'           => ['required','string','max:50', Rule::unique('productos','codigo')->ignore($producto->id)],
            'unidad_id'        => ['required','integer','exists:unidades,id'],
            'tipo_producto_id' => ['required','integer','exists:tipo_productos,id'],

            'activo' => ['nullable'],
            'stock'  => ['nullable'],
            'vende'  => ['nullable'],

            'minimo' => ['nullable','numeric','min:0'],
            'maximo' => ['nullable','numeric','min:0'],
            'obser'  => ['nullable','string','max:200'],
        ]);

        $producto->update([
            'name'             => trim($data['name']),
            'codigo'           => trim($data['codigo']),
            'unidad_id'        => $data['unidad_id'],
            'tipo_producto_id' => $data['tipo_producto_id'],

            'activo' => $request->boolean('activo'),
            'stock'  => $request->boolean('stock'),
            'vende'  => $request->boolean('vende'),

            'minimo' => $data['minimo'] ?? null,
            'maximo' => $data['maximo'] ?? null,
            'obser'  => isset($data['obser']) ? trim($data['obser']) : null,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
