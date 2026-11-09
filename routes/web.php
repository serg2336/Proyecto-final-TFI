<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductoController;
use App\Models\Producto;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $totalProductos = Producto::count();
        $stockCritico = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        $productosAReponer = Producto::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact('totalProductos', 'stockCritico', 'productosAReponer'));
    });

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    Route::get('/stock', function () {
        return view('stock');
    });

    Route::get('/pedidos', function () {
        return view('pedidos');
    });

    Route::get('/reportes', function () {
        return view('reportes');
    });

    Route::get('/clientes', function () {
        return view('clientes');
    });

    Route::get('/ventas', function () {
        return view('ventas');
    });
});
