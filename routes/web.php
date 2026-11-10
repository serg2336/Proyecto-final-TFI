<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    
    Route::get('/stock', function () { return view('stock'); })->name('stock');
    Route::get('/pedidos', function () { return view('pedidos'); })->name('pedidos');
    Route::get('/reportes', function () { return view('reportes'); })->name('reportes');
    Route::get('/ventas', function () { return view('ventas'); })->name('ventas');

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy')
        ->middleware('role:admin'); 

    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
});