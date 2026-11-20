<?php

use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
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

        $ultimosMovimientos = Movimiento::with('producto')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        return view('stock', compact('productos', 'ultimosMovimientos'));
    })->name('stock.index');

    // Pedidos y movimientos de stock: administrador y depósito.
    Route::middleware('role:administrador,deposito')->group(function () {
        // Rutas de Pedidos (de Ramiro)
        Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::patch('/pedidos/{pedido}/recibir', [PedidoController::class, 'recibir'])->name('pedidos.recibir');

        // Rutas de Movimientos (de Lía)
        Route::post('/stock/{producto}/movimientos', [MovimientoController::class, 'store'])
            ->name('movimientos.store');
    });

    // Ventas y clientes: administrador y vendedor.
    Route::middleware('role:administrador,vendedor')->group(function () {
        Route::get('/ventas', fn () => view('ventas'))->name('ventas.index');
        Route::get('/clientes', fn () => view('clientes'))->name('clientes.index');
    });

    // Reportes y gestión de usuarios: solo administrador.
    Route::middleware('role:administrador')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

        // Exportaciones de reportes en PDF y Excel.
        Route::get('/reportes/inventario/pdf', [ReporteController::class, 'inventarioPdf'])->name('reportes.inventario.pdf');
        Route::get('/reportes/inventario/excel', [ReporteController::class, 'inventarioExcel'])->name('reportes.inventario.excel');
        Route::get('/reportes/movimientos/pdf', [ReporteController::class, 'movimientosPdf'])->name('reportes.movimientos.pdf');
        Route::get('/reportes/movimientos/excel', [ReporteController::class, 'movimientosExcel'])->name('reportes.movimientos.excel');
        Route::get('/reportes/pedidos/pdf', [ReporteController::class, 'pedidosPdf'])->name('reportes.pedidos.pdf');
        Route::get('/reportes/pedidos/excel', [ReporteController::class, 'pedidosExcel'])->name('reportes.pedidos.excel');

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__.'/auth.php';