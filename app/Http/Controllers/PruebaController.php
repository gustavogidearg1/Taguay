<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebaController extends Controller
{
   
        public function __construct()
    {
        $this->middleware('auth'); // Aplica el middleware auth a todas las acciones del controlador
    }
    
    
    public function index()
    {
        return view('components.Prueba');
    }
}
