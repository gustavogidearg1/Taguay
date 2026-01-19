<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OrganizacionController extends Controller
{
    public function index(Request $request)
    {
        // opcional: permitir filtrar por updatedSince
        $updatedSince = (string) $request->query('updatedSince', '');

        // Cachear respuesta 10 min para no golpear Finneg siempre
        $cacheKey = 'finneg.organizaciones.' . md5($updatedSince);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($updatedSince) {

            // 1) Token cacheado 10 min
            $token = Cache::remember('finneg.access_token', now()->addMinutes(10), function () {
                $url = "https://api.finneg.com/api/oauth/token?grant_type=client_credentials&client_id=40901e9dcf89fc6da790af0e3c2a3cd2&client_secret=86514f5236398dbd16f0ded48d1b9b12";
                $r = Http::withHeaders(['Accept' => 'application/json'])->get($url);

                $t = trim($r->body()); // Finneg devuelve token como texto plano
                if ($t === '') abort(500, 'No se pudo obtener token Finneg');
                return $t;
            });

            // 2) Pegar a organizaciones
            $url = "https://api.finneg.com/api/Organizacion/list?updatedSince=" . urlencode($updatedSince) . "&ACCESS_TOKEN=" . urlencode($token);

            $resp = Http::withHeaders(['Accept' => 'application/json'])->get($url);

            if (!$resp->ok()) {
                abort($resp->status(), 'Error Finneg Organizacion/list');
            }

            return $resp->json();
        });
    }
}
