<?php

<<<<<<< HEAD
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Models\Producto;
=======
use App\Http\Controllers\ProfileController;
>>>>>>> b9250cf4d4c3d27f7445291093ec889bf4f836d1
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $totalProductos = Producto::count();
        $stockCritico = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        $productosAReponer = Producto::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->take(5)
            ->get();

<<<<<<< HEAD
        return view('dashboard', compact('totalProductos', 'stockCritico', 'productosAReponer'));
    })->name('dashboard');

    // Catálogo y stock: los tres roles pueden consultarlos.
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
=======
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/productos', function () {
        return view('productos');
    });
>>>>>>> b9250cf4d4c3d27f7445291093ec889bf4f836d1

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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
