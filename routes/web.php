<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalProductos = \App\Models\Producto::count();
    
    // Acá usamos whereColumn para comparar stock con stock_minimo
    $stockCritico = \App\Models\Producto::whereColumn('stock', '<=', 'stock_minimo')->count(); 
    
    $pedidosPendientes = 5; 
    $ventasDelMes = 450000; 

    return view('dashboard', compact('totalProductos', 'stockCritico', 'pedidosPendientes', 'ventasDelMes'));
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
        // Rutas de Pedidos a Proveedores
    Route::get('/pedidos', [App\Http\Controllers\PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos', [App\Http\Controllers\PedidoController::class, 'store'])->name('pedidos.store');
    Route::patch('/pedidos/{pedido}/recibir', [App\Http\Controllers\PedidoController::class, 'recibir'])->name('pedidos.recibir');
    
   Route::get('/stock', function () { 
    $productos = \App\Models\Producto::all();
    return view('stock', compact('productos')); 
})->name('stock');
   
    
    Route::get('/reportes', function () { return view('reportes'); })->name('reportes');
    Route::get('/ventas', function () { return view('ventas'); })->name('ventas');

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy')
        ->middleware('role:admin'); 

    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        // Ruta para el control de stock crítico
    Route::get('/stock-critico', [ProductoController::class, 'stockCritico'])->name('productos.stock_critico');
});


