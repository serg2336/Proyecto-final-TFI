<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

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
});
