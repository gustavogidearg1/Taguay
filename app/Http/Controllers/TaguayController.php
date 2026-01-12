<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaguayController extends Controller
{
    public function getJsonData()
    {
        // Incluir el archivo PHP que genera el JSON
        require_once app_path('Services/appTAGUAYAnalisisContratoVentaGrano.php');

        // El archivo ya genera el JSON, así que no necesitas hacer nada más.
        // Laravel automáticamente enviará la salida al cliente.
    }
}