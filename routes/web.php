<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EntradaInventarioController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\IngresoManualController;
use App\Http\Controllers\ConteoInventarioController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'no-cache'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Solo administrador
    Route::middleware('role:administrador')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['index']);
        Route::resource('recetas',   RecetaController::class);
        Route::resource('insumos',   InsumoController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('entradas',  EntradaInventarioController::class)->only(['index', 'create', 'store']);
        Route::resource('gastos',    GastoController::class)->only(['index', 'create', 'store']);
        Route::resource('ingresos',  IngresoManualController::class)->only(['create', 'store']);
    });

    // Administrador y empleado
    Route::middleware('role:administrador,empleado')->group(function () {
        Route::resource('ventas',   VentaController::class)->only(['index', 'create', 'store']);
        Route::resource('insumos',  InsumoController::class)->only(['index']);
        Route::resource('conteos',  ConteoInventarioController::class)->only(['index', 'create', 'store', 'show']);
    });

});
