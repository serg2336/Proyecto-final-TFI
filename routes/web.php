<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/productos', function () {
        return view('productos');
    });

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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
