@extends('layouts.app')

@section('titulo_pagina', 'Control de Stock')

@section('content')
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Movimientos de Inventario</h3>
            <p class="text-gray-500 text-sm">Registrá entradas y salidas de mercadería.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
                <span>⬆️</span> Registrar Entrada
            </button>
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
                <span>⬇️</span> Registrar Salida
            </button>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Fecha</th>
                    <th class="py-4 px-6 border-b">Producto</th>
                    <th class="py-4 px-6 border-b">Tipo</th>
                    <th class="py-4 px-6 border-b">Cantidad</th>
                    <th class="py-4 px-6 border-b">Usuario</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6">Hoy, 10:30</td>
                    <td class="py-4 px-6 font-semibold">Filtro de Aceite Fiat</td>
                    <td class="py-4 px-6"><span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">Entrada</span></td>
                    <td class="py-4 px-6 font-bold">+ 20</td>
                    <td class="py-4 px-6">Soria, Sergio</td>
                </tr>
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6">Hoy, 09:15</td>
                    <td class="py-4 px-6 font-semibold">Bujía NGK</td>
                    <td class="py-4 px-6"><span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs font-bold">Salida</span></td>
                    <td class="py-4 px-6 font-bold">- 4</td>
                    <td class="py-4 px-6">Corrales, Ramiro</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection