<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EntradaInventarioController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\IngresoManualController;
use App\Http\Controllers\ConteoInventarioController;
use App\Http\Controllers\UserController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/manifest.json', fn() =>
    response()->view('manifest')->header('Content-Type', 'application/manifest+json')
)->name('manifest');

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'no-cache'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/ping-session', fn() => response()->json(['ok' => true]))->name('ping.session');

    // Solo administrador
    Route::middleware('role:administrador')->group(function () {
        Route::resource('productos', ProductoController::class)->only(['index']);
        Route::resource('recetas',   RecetaController::class);
        Route::get('insumos/estadisticas', [InsumoController::class, 'estadisticas'])->name('insumos.estadisticas');
        Route::resource('insumos',   InsumoController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('entradas',  EntradaInventarioController::class)->only(['index', 'create', 'store']);
        Route::resource('gastos',    GastoController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('ingresos',  IngresoManualController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('usuarios',  UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // Administrador y empleado
    Route::middleware('role:administrador,empleado')->group(function () {
        Route::resource('ventas',   VentaController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('insumos',  InsumoController::class)->only(['index']);
        Route::resource('conteos',  ConteoInventarioController::class)->only(['index', 'create', 'store', 'show']);
    });

});
