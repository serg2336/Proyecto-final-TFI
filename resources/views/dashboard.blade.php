@extends('layouts.app')

@section('titulo_pagina', 'Dashboard - Resumen General')

@section('content')
    <!-- Hero con la rueda de ITAL FIAT -->
    <section class="app-hero">
        <div>
            <p class="app-eyebrow"><span class="tick"></span>Panel de control · Inventario</p>
            <h1 class="app-hero-title">Panel de<br><span class="accent">Control</span></h1>
            <p class="app-hero-lead">
                Cada producto, en su lugar.<br>
                Información precisa para decisiones precisas.
            </p>
        </div>

        <div class="app-wheel" aria-hidden="true">
            <svg class="app-gear" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="5">
                <circle cx="100" cy="100" r="62"/>
                <circle cx="100" cy="100" r="24"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(0 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(45 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(90 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(135 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(180 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(225 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(270 100 100)"/>
                <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(315 100 100)"/>
            </svg>

            <div class="app-stamp">
                <div>
                    <strong>ITAL<br>FIAT</strong>
                    <span>Repuestos</span>
                </div>
            </div>
        </div>
    </section>

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
                        @forelse($ultimosMovimientos as $movimiento)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="py-3 px-6">{{ $movimiento->producto?->descripcion ?? 'Producto eliminado' }}</td>
                                <td class="py-3 px-6">
                                    @if($movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA)
                                        <span class="text-green-600 font-semibold bg-green-100 px-2 py-1 rounded text-xs">Ingreso</span>
                                    @else
                                        <span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded text-xs">Salida</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6">
                                    {{ $movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA ? '+' : '-' }} {{ $movimiento->cantidad }}
                                </td>
                                <td class="py-3 px-6">{{ $movimiento->created_at->locale('es')->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 px-6 text-center text-gray-500">
                                    <p class="font-semibold text-gray-700">Todavía no hay movimientos registrados</p>
                                    <p class="text-sm">Los ingresos y salidas de stock van a aparecer acá.</p>
                                </td>
                            </tr>
                        @endforelse
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