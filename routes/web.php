<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Dashboard con indicadores reales del inventario.
    Route::get('/', function () {
        $totalProductos = Producto::count();
        $stockCritico = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        $productosAReponer = Producto::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->take(5)
            ->get();

        $ultimosMovimientos = Movimiento::with('producto')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', compact('totalProductos', 'stockCritico', 'productosAReponer', 'ultimosMovimientos'));
    })->name('dashboard');

    // Perfil del usuario autenticado (Breeze).
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catálogo y stock: los tres roles pueden consultarlos.
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy')
        ->middleware('role:administrador');

    Route::get('/stock', function () {
        $productos = Producto::query()->orderBy('stock')->orderBy('descripcion')->get();

        return view('stock', compact('productos'));
    })->name('stock.index');

    // Pedidos: administrador y depósito.
    Route::middleware('role:administrador,deposito')->group(function () {
        Route::get('/pedidos', fn () => view('pedidos'))->name('pedidos.index');
    });

    // Ventas y clientes: administrador y vendedor.
    Route::middleware('role:administrador,vendedor')->group(function () {
        Route::get('/ventas', fn () => view('ventas'))->name('ventas.index');
        Route::get('/clientes', fn () => view('clientes'))->name('clientes.index');
    });

    // Reportes y gestión de usuarios: solo administrador.
    Route::middleware('role:administrador')->group(function () {
        Route::get('/reportes', fn () => view('reportes'))->name('reportes.index');

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__.'/auth.php';
