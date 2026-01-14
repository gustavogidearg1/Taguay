<?php

namespace App\Http\Controllers;

use App\Models\Hacienda;
use App\Models\Categoria;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\HaciendaCreatedMail;

class HaciendaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:ver_ganadero']);

        // ✅ Aplica HaciendaPolicy automáticamente a:
        // index/show/create/store/edit/update/destroy
        $this->authorizeResource(Hacienda::class, 'hacienda');
    }

    public function index(Request $r)
    {
        $q = Hacienda::query()
            ->with(['categoria', 'establecimiento', 'user'])
            ->orderByDesc('id');

        // ✅ Si NO es admin, solo lo suyo
        if (!auth()->user()->hasRole('admin')) {
            $q->where('user_id', auth()->id());
        }

        if ($s = $r->get('s')) {
            $q->where(function ($w) use ($s) {
                $w->where('cliente', 'like', "%{$s}%")
                  ->orWhere('consignatario', 'like', "%{$s}%")
                  ->orWhere('vendedor', 'like', "%{$s}%")
                  ->orWhere('destino', 'like', "%{$s}%")
                  ->orWhere('patente', 'like', "%{$s}%");
            });
        }

        $rows = $q->paginate(20)->withQueryString();

        return view('abm.haciendas.index', compact('rows'));
    }

    public function create()
    {
        return view('abm.haciendas.create', [
            'categorias'       => Categoria::orderBy('nombre')->get(),
            'establecimientos' => Establecimiento::orderBy('nombre')->get(),
            'entry'            => new Hacienda(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'cliente'            => 'required|string|max:200',
            'consignatario'      => 'nullable|string|max:200',
            'vendedor'           => 'nullable|string|max:200',
            'categoria_id'       => 'required|exists:categorias,id',
            'cantidad'           => 'required|numeric|min:0|max:9999999999.9',
            'transportista'      => 'nullable|string|max:100',
            'patente'            => 'nullable|string|max:50',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'destino'            => 'nullable|string|max:200',
            'peso_vivo_menos_8'  => 'nullable|numeric|min:0|max:9999999999.9',
        ]);

        $data['user_id'] = auth()->id();

        $entry = Hacienda::create($data);

        if ($r->user()?->email) {
            Mail::to($r->user()->email)
                ->cc('logistica@taguay.com.ar')
                ->send(new HaciendaCreatedMail($entry));
        }

        return redirect()
            ->route('haciendas.show', $entry)
            ->with('ok', 'Hacienda creada correctamente y correo enviado.');
    }

    public function show(Hacienda $hacienda)
    {
        $hacienda->load(['categoria', 'establecimiento', 'user']);
        return view('abm.haciendas.show', compact('hacienda'));
    }

    public function edit(Hacienda $hacienda)
    {
        return view('abm.haciendas.edit', [
            'categorias'       => Categoria::orderBy('nombre')->get(),
            'establecimientos' => Establecimiento::orderBy('nombre')->get(),
            'entry'            => $hacienda,
        ]);
    }

    public function update(Request $r, Hacienda $hacienda)
    {
        $data = $r->validate([
            'cliente'            => 'required|string|max:200',
            'consignatario'      => 'nullable|string|max:200',
            'vendedor'           => 'nullable|string|max:200',
            'categoria_id'       => 'required|exists:categorias,id',
            'cantidad'           => 'required|numeric|min:0|max:9999999999.9',
            'transportista'      => 'nullable|string|max:100',
            'patente'            => 'nullable|string|max:50',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'destino'            => 'nullable|string|max:200',
            'peso_vivo_menos_8'  => 'nullable|numeric|min:0|max:9999999999.9',
        ]);

        $hacienda->update($data);

        return redirect()
            ->route('haciendas.show', $hacienda)
            ->with('ok', 'Hacienda actualizada.');
    }

    public function destroy(Hacienda $hacienda)
    {
        $hacienda->delete();
        return redirect()->route('haciendas.index')->with('ok', 'Hacienda eliminada.');
    }
}
