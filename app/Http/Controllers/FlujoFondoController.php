<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FlujoFondoController extends Controller
{
    
        public function __construct()
    {
        $this->middleware('auth'); // Aplica el middleware auth a todas las acciones del controlador
    }
    
    
    public function index()
    {
        return view('components.flujo-fondo'); // Retorna la vista flujo-fondo.blade.php
    }
}
