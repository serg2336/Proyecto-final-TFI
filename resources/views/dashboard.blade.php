@extends('layouts.app')

@section('titulo_pagina', 'Dashboard - Resumen General')

@section('content')
    <!-- Tarjetas de Indicadores -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        <!-- Tarjeta 1 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 font-semibold mb-2">Total Productos</p>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalProductos, 0, ',', '.') }}</p>
        </div>

        <!-- Tarjeta 2 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-500 font-semibold mb-2">Stock Crítico (Alertas)</p>
            <p class="text-3xl font-bold text-red-500">{{ number_format($stockCritico, 0, ',', '.') }}</p>
        </div>

        <!-- Tarjeta 3 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 font-semibold mb-2">Pedidos Pendientes</p>
            <p class="text-3xl font-bold text-gray-800">5</p>
        </div>

        <!-- Tarjeta 4 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500 font-semibold mb-2">Ventas del Mes</p>
            <p class="text-3xl font-bold text-gray-800">$450,000</p>
        </div>
    </div>

    <!-- Contenido dividido -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tabla de Últimos Movimientos (Ocupa 2 columnas) -->
        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Últimos Movimientos de Stock</h3>
                <button class="text-sm text-blue-600 hover:underline">Ver todo</button>
            </div>
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm">
                            <th class="py-3 px-6 border-b">Producto</th>
                            <th class="py-3 px-6 border-b">Tipo</th>
                            <th class="py-3 px-6 border-b">Cant.</th>
                            <th class="py-3 px-6 border-b">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="py-3 px-6">Filtro de Aceite Fiat</td>
                            <td class="py-3 px-6"><span class="text-green-600 font-semibold bg-green-100 px-2 py-1 rounded text-xs">Ingreso</span></td>
                            <td class="py-3 px-6">+ 20</td>
                            <td class="py-3 px-6">Hoy, 10:30</td>
                        </tr>
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="py-3 px-6">Bujía NGK</td>
                            <td class="py-3 px-6"><span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded text-xs">Salida</span></td>
                            <td class="py-3 px-6">- 4</td>
                            <td class="py-3 px-6">Hoy, 09:15</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6">Pastillas de Freno</td>
                            <td class="py-3 px-6"><span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded text-xs">Salida</span></td>
                            <td class="py-3 px-6">- 2</td>
                            <td class="py-3 px-6">Ayer, 16:45</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel de Alertas (Ocupa 1 columna) -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Repuestos a Reponer</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($productosAReponer as $producto)
                    <li class="p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $producto->descripcion }}</p>
                            <p class="text-xs text-gray-500">
                                Quedan solo {{ $producto->stock }} unidades (mínimo {{ $producto->stock_minimo }})
                            </p>
                        </div>
                        <span class="text-red-500 text-xl">⚠️</span>
                    </li>
                @empty
                    <li class="p-4 text-sm text-gray-500">
                        No hay repuestos con stock crítico. 👌
                    </li>
                @endforelse
            </ul>
        </div>

    </div>
@endsection