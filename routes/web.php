<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');

});
Route::get('/productos', function () {
    return view('productos');
});
Route::get('/login', function () {
    return view('login');
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