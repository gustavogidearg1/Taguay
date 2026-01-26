<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;

class OrganizacionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $activo = $request->query('activo', ''); // '' | '1' | '0'

        $organizaciones = Organizacion::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('codigo', 'like', "%{$q}%");
                });
            })
            ->when($activo !== '', fn ($qq) => $qq->where('activo', (int) $activo))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('abm.organizaciones.index', compact('organizaciones', 'q', 'activo'));
    }

    public function create()
    {
        return view('abm.organizaciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'      => 'required|string|max:50|unique:organizaciones,codigo',
            'name'        => 'required|string|max:150|unique:organizaciones,name',
            'fecha'       => 'required|date',
            'descripcion' => 'nullable|string',
            'activo'      => 'required|boolean',
        ]);

        Organizacion::create($data);

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización creada exitosamente.');
    }

    public function show(Organizacion $organizacion)
    {
        return view('abm.organizaciones.show', compact('organizacion'));
    }

    public function edit(Organizacion $organizacion)
    {
        return view('abm.organizaciones.edit', compact('organizacion'));
    }

    public function update(Request $request, Organizacion $organizacion)
    {
        $data = $request->validate([
            'codigo'      => 'required|string|max:50|unique:organizaciones,codigo,' . $organizacion->id,
            'name'        => 'required|string|max:150|unique:organizaciones,name,' . $organizacion->id,
            'fecha'       => 'required|date',
            'descripcion' => 'nullable|string',
            'activo'      => 'required|boolean',
        ]);

        $organizacion->update($data);

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización actualizada exitosamente.');
    }

    public function destroy(Organizacion $organizacion)
    {
        $organizacion->delete();

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización eliminada exitosamente.');
    }
}
