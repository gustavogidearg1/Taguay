<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HaciendaController;
use App\Http\Controllers\LluviaController;

use App\Http\Controllers\MargenBrutoController;
use App\Http\Controllers\CosechaController;
use App\Http\Controllers\FlujoFondoController;
use App\Http\Controllers\TaguayController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Públicas o generales (si querés que requieran login, movelas al group auth)
Route::get('/getJsonData', [TaguayController::class, 'getJsonData'])->name('getJsonData');

Route::middleware(['auth'])->group(function () {

    // Pantallas generales
    Route::get('/margen-bruto', [MargenBrutoController::class, 'index'])->name('margen-bruto');
    Route::get('/cosecha', [CosechaController::class, 'index'])->name('cosecha');
    Route::get('/flujo-fondo', [FlujoFondoController::class, 'index'])->name('flujo-fondo');

    // TEST (si ya no lo usás, sacalo)
    Route::get('/test-flujo', function() {
        return app()->make(FlujoFondoController::class)->index();
    })->name('test-flujo');

    // ABM Usuarios (solo admin)
    Route::resource('users', UserController::class)->middleware('role:admin');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.resetPassword')
        ->middleware('role:admin');

    // Recursos por permisos (si querés que haciendas/lluvias dependan de permisos)
    Route::resource('haciendas', HaciendaController::class)->middleware('permission:ver_ganadero');
    Route::resource('lluvias', LluviaController::class)->middleware('permission:ver_agricola');

    Route::post('/lluvias/{lluvia}/resend-mail', [LluviaController::class, 'resendMail'])
        ->name('lluvias.resendMail')
        ->middleware('permission:ver_agricola');

    // Dashboards simples
    Route::get('/agricola', fn() => view('agricola.index'))
        ->name('agricola.index')
        ->middleware('permission:ver_agricola');

    Route::get('/ganadero', fn() => view('ganadero.index'))
        ->name('ganadero.index')
        ->middleware('permission:ver_ganadero');

    Route::get('/comercial', fn() => view('comercial.index'))
        ->name('comercial.index')
        ->middleware('permission:ver_comercial');
});
