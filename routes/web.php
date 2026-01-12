<?php

use App\Http\Controllers\CosechaController;
use App\Http\Controllers\FlujoFondoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MargenBrutoController;
use App\Http\Controllers\TaguayController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HaciendaController;
use App\Http\Controllers\LluviaController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/margen-bruto', [MargenBrutoController::class, 'index'])->name('margen-bruto');


// Ruta para la página de Cosecha
Route::get('/cosecha', [CosechaController::class, 'index'])->name('cosecha');

// Ruta para la página de Flujo de Fondo
Route::get('/flujo-fondo', [FlujoFondoController::class, 'index'])->name('flujo-fondo');

// Definir la ruta para acceder al JSON
Route::get('/getJsonData', [TaguayController::class, 'getJsonData']);

// En routes/web.php
Route::get('/test-flujo', function() {
    return app()->make('App\Http\Controllers\FlujoFondoController')->index();
});

Route::middleware(['auth'])->group(function () {
    // Ruta resource usando la política directamente
    Route::resource('users', UserController::class)
         ->middleware('can:viewAny,App\Models\User');
});

Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
    ->name('users.resetPassword');

Route::middleware(['auth'])->group(function () {
    Route::resource('haciendas', HaciendaController::class);
});

Route::middleware(['auth'])
->group(function(){
Route::resource('lluvias', LluviaController::class);
});

Route::post('/lluvias/{lluvia}/resend-mail', [LluviaController::class, 'resendMail'])
     ->name('lluvias.resendMail')
     ->middleware('auth');


