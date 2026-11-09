<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

// 1. Ruta principal (Dashboard)
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// 2. Rutas SIN protección de login (Temporal, porque falta auth.php)
// Route::middleware('auth')->group(function () {
    
    // Vistas estáticas
    Route::get('/stock', function () { return view('stock'); })->name('stock');
    Route::get('/pedidos', function () { return view('pedidos'); })->name('pedidos');
    Route::get('/reportes', function () { return view('reportes'); })->name('reportes');
    Route::get('/clientes', function () { return view('clientes'); })->name('clientes');
    Route::get('/ventas', function () { return view('ventas'); })->name('ventas');

    // Rutas de Productos
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    
    // ¡ACÁ ESTÁ LA HU2! La dejamos activa para probar el rol
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy')
        ->middleware('checkrol:admin'); 

// }); // <--- Cerramos el grupo del middleware (comentado)
// RUTA TEMPORAL PARA PROBAR EL MIDDLEWARE (Loguea al usuario 1 por la fuerza)
Route::get('/fake-login', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(1); // Loguea al usuario con ID 1
    return "¡Logueado como el usuario 1 (Admin) con éxito! Andá a /productos";
});