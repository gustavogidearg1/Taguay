<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstablecimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // solo administradores
    }

    /* =========================
       INDEX
    ========================== */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $sort   = $request->get('sort', 'nombre');   // nombre | ubicacion | created_at
        $order  = $request->get('order', 'asc');     // asc | desc
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10,20,50,100], true)) $perPage = 10;

        $q = Establecimiento::query();

        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        $allowed = ['nombre','ubicacion','id','created_at'];
        if (!in_array($sort, $allowed, true)) $sort = 'nombre';
        if (!in_array($order, ['asc','desc'], true)) $order = 'asc';

        $establecimientos = $q->orderBy($sort, $order)
            ->paginate($perPage)
            ->appends([
                'q' => $search,
                'sort' => $sort,
                'order' => $order,
                'per_page' => $perPage,
            ]);

        return view('abm.establecimientos.index', compact(
            'establecimientos','search','sort','order','perPage'
        ));
    }

    /* =========================
       CREATE
    ========================== */
    public function create()
    {
        return view('abm.establecimientos.create');
    }

    /* =========================
       STORE
    ========================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => ['required','string','max:255'],
            'ubicacion' => ['nullable','string','max:255'],
        ]);

        Establecimiento::create($data);

        return redirect()->route('establecimientos.index')
            ->with('success', 'Establecimiento creado exitosamente');
    }

    /* =========================
       EDIT
    ========================== */
    public function edit(Establecimiento $establecimiento)
    {
        return view('abm.establecimientos.edit', compact('establecimiento'));
    }

    /* =========================
       UPDATE
    ========================== */
    public function update(Request $request, Establecimiento $establecimiento)
    {
        $data = $request->validate([
            'nombre'    => ['required','string','max:255'],
            'ubicacion' => ['nullable','string','max:255'],
        ]);

        $establecimiento->update($data);

        return redirect()->route('establecimientos.index')
            ->with('success', 'Establecimiento actualizado exitosamente');
    }

    /* =========================
       DESTROY
    ========================== */
    public function destroy(Establecimiento $establecimiento)
    {
        // Opcional: impedir borrar si tiene haciendas asociadas
        if ($establecimiento->haciendas()->exists()) {
            return back()->with('error', 'No se puede eliminar: el establecimiento tiene haciendas asociadas.');
        }

        $establecimiento->delete();

        return redirect()->route('establecimientos.index')
            ->with('success', 'Establecimiento eliminado exitosamente');
    }
}
