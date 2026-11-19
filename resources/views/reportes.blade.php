@extends('layouts.app')

@section('titulo_pagina', 'Reportes y Estadísticas')

@section('content')
    @php
        $params = [
            'desde' => $desde->format('Y-m-d'),
            'hasta' => $hasta->format('Y-m-d'),
        ];

        $estadosPedido = [
            'pendiente' => ['label' => 'Pendiente', 'clase' => 'text-yellow-600 bg-yellow-100'],
            'recibido' => ['label' => 'Recibido', 'clase' => 'text-green-600 bg-green-100'],
            'cancelado' => ['label' => 'Cancelado', 'clase' => 'text-red-600 bg-red-100'],
        ];
    @endphp

    <!-- Encabezado -->
    <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Generación de reportes</h3>
            <p class="text-gray-500 text-sm">Exportá la información real del sistema para tomar decisiones.</p>
        </div>
        <p class="text-sm text-gray-500">
            Período:
            <strong class="text-gray-700">{{ $desde->format('d/m/Y') }}</strong>
            al
            <strong class="text-gray-700">{{ $hasta->format('d/m/Y') }}</strong>
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-600 text-white text-sm rounded-lg px-4 py-3" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filtro por fechas -->
    <form method="GET" action="{{ route('reportes.index') }}" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label for="desde" class="block text-sm font-semibold text-gray-700 mb-1">Desde</label>
            <input
                type="date"
                id="desde"
                name="desde"
                value="{{ old('desde', $desde->format('Y-m-d')) }}"
                class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div>
            <label for="hasta" class="block text-sm font-semibold text-gray-700 mb-1">Hasta</label>
            <input
                type="date"
                id="hasta"
                name="hasta"
                value="{{ old('hasta', $hasta->format('Y-m-d')) }}"
                class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
            Aplicar filtro
        </button>

        @if (request()->filled('desde') || request()->filled('hasta'))
            <a href="{{ route('reportes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
                Limpiar fechas
            </a>
        @endif
    </form>

    <!-- Indicadores -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Productos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($inventario['productos'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">En el catálogo</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unidades en stock</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($inventario['unidades'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Suma de existencias</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Valor del inventario</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($inventario['valor'], 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Precio × stock</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Bajo mínimo</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($inventario['bajoMinimo'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Requieren reposición</p>
        </div>
    </div>

    <!-- Reporte de inventario -->
    <section class="mb-8">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
            <div>
                <h4 class="text-lg font-bold text-gray-800">Reporte de inventario</h4>
                <p class="text-sm text-gray-500">Listado completo de productos con stock y valorización.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.inventario.pdf', $params) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📄</span> Exportar a PDF
                </a>
                <a href="{{ route('reportes.inventario.excel', $params) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📊</span> Exportar a Excel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="py-3 px-4 border-b">Código</th>
                            <th class="py-3 px-4 border-b">Producto</th>
                            <th class="py-3 px-4 border-b">Marca / Modelo</th>
                            <th class="py-3 px-4 border-b text-right">Precio</th>
                            <th class="py-3 px-4 border-b text-center">Stock</th>
                            <th class="py-3 px-4 border-b">Estado</th>
                            <th class="py-3 px-4 border-b text-right">Valorización</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($productos as $producto)
                            <tr @class(['hover:bg-gray-50 border-b', 'bg-red-100' => $producto->stock <= $producto->stock_minimo])>
                                <td class="py-3 px-4 font-mono text-sm">{{ $producto->codigo }}</td>
                                <td class="py-3 px-4 font-semibold">{{ $producto->descripcion }}</td>
                                <td class="py-3 px-4 text-sm">
                                    {{ $producto->marca }}@if ($producto->modelo) / {{ $producto->modelo }}@endif
                                </td>
                                <td class="py-3 px-4 text-right">${{ number_format((float) $producto->precio, 2, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center font-bold">{{ $producto->stock }}</td>
                                <td class="py-3 px-4">
                                    @if ($producto->stock === 0)
                                        <span class="text-white bg-red-800 px-2 py-1 rounded text-xs font-bold">Sin stock</span>
                                    @elseif ($producto->stock <= $producto->stock_minimo)
                                        <span class="text-white bg-red-600 px-2 py-1 rounded text-xs font-bold">Stock bajo</span>
                                    @else
                                        <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">Disponible</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-semibold">
                                    ${{ number_format((float) $producto->precio * (int) $producto->stock, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                    Todavía no hay productos cargados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold text-gray-800">
                            <td colspan="6" class="py-3 px-4 text-right">Total del inventario</td>
                            <td class="py-3 px-4 text-right">${{ number_format($inventario['valor'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <!-- Reporte de movimientos -->
    <section class="mb-8">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
            <div>
                <h4 class="text-lg font-bold text-gray-800">Reporte de movimientos de stock</h4>
                <p class="text-sm text-gray-500">
                    Entradas y salidas del período:
                    <strong class="text-green-600">+{{ $resumenMovimientos['entradas'] }}</strong> entradas ·
                    <strong class="text-red-500">-{{ $resumenMovimientos['salidas'] }}</strong> salidas ·
                    {{ $resumenMovimientos['total'] }} movimiento(s).
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.movimientos.pdf', $params) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📄</span> Exportar a PDF
                </a>
                <a href="{{ route('reportes.movimientos.excel', $params) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📊</span> Exportar a Excel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="py-3 px-4 border-b">Fecha</th>
                            <th class="py-3 px-4 border-b">Producto</th>
                            <th class="py-3 px-4 border-b">Tipo</th>
                            <th class="py-3 px-4 border-b text-center">Cantidad</th>
                            <th class="py-3 px-4 border-b">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($movimientos as $movimiento)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $movimiento->created_at->locale('es')->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4">
                                    {{ $movimiento->producto?->descripcion ?? 'Producto eliminado' }}
                                    <span class="text-xs text-gray-400 block">{{ $movimiento->producto?->codigo ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA)
                                        <span class="text-green-600 font-semibold bg-green-100 px-2 py-1 rounded text-xs">Entrada</span>
                                    @else
                                        <span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded text-xs">Salida</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-semibold">
                                    {{ $movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA ? '+' : '-' }} {{ $movimiento->cantidad }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $movimiento->motivo ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                    No hay movimientos registrados en el período seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Reporte de pedidos -->
    <section class="mb-8">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
            <div>
                <h4 class="text-lg font-bold text-gray-800">Reporte de pedidos a proveedores</h4>
                <p class="text-sm text-gray-500">
                    {{ $resumenPedidos['total'] }} pedido(s) ·
                    <strong class="text-yellow-600">{{ $resumenPedidos['pendientes'] }}</strong> pendiente(s) ·
                    <strong class="text-green-600">{{ $resumenPedidos['recibidos'] }}</strong> recibido(s).
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.pedidos.pdf', $params) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📄</span> Exportar a PDF
                </a>
                <a href="{{ route('reportes.pedidos.excel', $params) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span aria-hidden="true">📊</span> Exportar a Excel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="py-3 px-4 border-b">Nº Pedido</th>
                            <th class="py-3 px-4 border-b">Producto</th>
                            <th class="py-3 px-4 border-b">Proveedor</th>
                            <th class="py-3 px-4 border-b text-center">Cantidad</th>
                            <th class="py-3 px-4 border-b">Estado</th>
                            <th class="py-3 px-4 border-b">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($pedidos as $pedido)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="py-3 px-4 font-mono text-sm">PED-{{ str_pad($pedido->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-3 px-4">
                                    {{ $pedido->producto?->descripcion ?? 'Producto eliminado' }}
                                    <span class="text-xs text-gray-400 block">{{ $pedido->producto?->codigo ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $pedido->proveedor ?: 'Sin proveedor' }}</td>
                                <td class="py-3 px-4 text-center font-semibold">{{ $pedido->cantidad }}</td>
                                <td class="py-3 px-4">
                                    <span class="{{ $estadosPedido[$pedido->estado]['clase'] ?? 'text-gray-600 bg-gray-100' }} px-2 py-1 rounded text-xs font-bold">
                                        {{ $estadosPedido[$pedido->estado]['label'] ?? ucfirst($pedido->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $pedido->created_at->locale('es')->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                    No hay pedidos registrados en el período seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
