<?php

namespace App\Http\Controllers;

use App\Models\LoteEstado;
use App\Support\Permisos;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Pantalla de inicio: tarjetas de reportes según permisos y,
     * para usuarios Agrícolas, la tabla de últimos Estados por Lote.
     */
    public function index()
    {
        $user = Auth::user();

        // ¿Ve al menos una tarjeta de reporte?
        $tieneReportes = $user->canAny(array_keys(Permisos::REPORTES));

        // Últimos movimientos de Estados por Lote (admin: todos; resto: solo los propios)
        $registros = collect();
        if ($user->can('ver_agricola')) {
            $registros = LoteEstado::with(['lote.establecimiento', 'lote.campania', 'lote.cultivo', 'cultivo', 'estadoCultivo', 'user'])
                ->visiblesPara($user)
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->limit(10)
                ->get();
        }

        // La tabla arranca abierta solo si no hay tarjetas de reportes para mostrar
        $tablaAbierta = ! $tieneReportes;

        return view('home', compact('tieneReportes', 'registros', 'tablaAbierta'));
    }
}
