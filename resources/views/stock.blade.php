@extends('layouts.app')

@section('titulo_pagina', 'Stock Actual')

@section('content')
    @php
        $puedeGestionar = auth()->user()->hasAnyRole(
            \App\Models\User::ROLE_ADMIN,
            \App\Models\User::ROLE_DEPOSITO
        );

        $productosBajos = $productos->filter(
            fn ($producto) => $producto->stock <= $producto->stock_minimo
        );
    @endphp

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Stock actual</h3>
            <p class="text-gray-500 text-sm">Revisá existencias y detectá faltantes.</p>
        </div>
        <a href="{{ route('productos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>📦</span> Gestionar productos
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($productosBajos->isNotEmpty())
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg px-4 py-3">
            <p class="font-semibold">⚠️ Atención: {{ $productosBajos->count() }} repuesto(s) con stock bajo o agotado.</p>
            <p class="text-sm">{{ $productosBajos->pluck('descripcion')->join(', ') }}</p>
        </div>
    @endif

    <!-- Existencias actuales -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Código</th>
                    <th class="py-4 px-6 border-b">Producto</th>
                    <th class="py-4 px-6 border-b">Marca / Modelo</th>
                    <th class="py-4 px-6 border-b">Stock</th>
                    <th class="py-4 px-6 border-b">Estado</th>
                    @if ($puedeGestionar)
                        <th class="py-4 px-6 border-b text-center">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($productos as $producto)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="py-4 px-6 font-mono text-sm">{{ $producto->codigo }}</td>
                        <td class="py-4 px-6 font-semibold">
                            {{ $producto->descripcion }}
                            <div class="text-xs text-gray-400 font-normal">Actualizado {{ $producto->updated_at->locale('es')->diffForHumans() }}</div>
                        </td>
                        <td class="py-4 px-6">
                            {{ $producto->marca }}@if($producto->modelo) / {{ $producto->modelo }}@endif
                        </td>
                        <td class="py-4 px-6 font-bold">{{ $producto->stock }} un.</td>
                        <td class="py-4 px-6">
                            @if($producto->stock === 0)
                                <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs font-bold" title="Este producto no tiene unidades disponibles.">Sin stock</span>
                            @elseif($producto->stock <= $producto->stock_minimo)
                                <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs font-bold" title="Quedan pocas unidades. Revisá el umbral de reposición.">Stock bajo</span>
                            @else
                                <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">Disponible</span>
                            @endif
                        </td>
                        @if ($puedeGestionar)
                            <td class="py-4 px-6 text-center">
                                <button
                                    type="button"
                                    class="text-blue-500 hover:text-blue-700 font-semibold text-sm"
                                    onclick="openMovimientoModal(this)"
                                    data-id="{{ $producto->id }}"
                                    data-codigo="{{ $producto->codigo }}"
                                    data-descripcion="{{ $producto->descripcion }}"
                                    data-stock="{{ $producto->stock }}"
                                >Movimiento</button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $puedeGestionar ? 6 : 5 }}" class="py-10 px-6 text-center text-gray-500">
                            <p class="font-semibold text-gray-700">Todavía no hay productos</p>
                            <p class="text-sm">Cuando agregues el primero, va a aparecer acá.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Historial de movimientos -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Historial de movimientos</h3>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 border-b">Fecha</th>
                        <th class="py-4 px-6 border-b">Producto</th>
                        <th class="py-4 px-6 border-b">Tipo</th>
                        <th class="py-4 px-6 border-b">Cantidad</th>
                        <th class="py-4 px-6 border-b">Motivo</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($ultimosMovimientos as $movimiento)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $movimiento->created_at->locale('es')->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6">{{ $movimiento->producto?->descripcion ?? 'Producto eliminado' }}</td>
                            <td class="py-4 px-6">
                                @if($movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA)
                                    <span class="text-green-600 font-semibold bg-green-100 px-2 py-1 rounded text-xs">Entrada</span>
                                @else
                                    <span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded text-xs">Salida</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                {{ $movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA ? '+' : '-' }} {{ $movimiento->cantidad }}
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $movimiento->motivo ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 px-6 text-center text-gray-500">
                                <p class="font-semibold text-gray-700">Todavía no hay movimientos registrados</p>
                                <p class="text-sm">Registrá una entrada o salida para ver el historial.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($puedeGestionar)
        <!-- MODAL DE MOVIMIENTO DE STOCK -->
        <div id="modalMovimiento" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Registrar movimiento</h3>
                    <button onclick="toggleMovimientoModal()" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
                </div>

                <form id="formMovimiento" action="{{ route('movimientos.store', ['producto' => '__ID__']) }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="producto_id" id="movimiento-producto-id" value="{{ old('producto_id') }}">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="mb-4 text-sm text-gray-600">
                        Producto: <strong id="movimiento-producto-nombre">—</strong>
                        · Stock actual: <strong id="movimiento-producto-stock">—</strong> un.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de movimiento</label>
                            <select name="tipo" id="movimiento-tipo" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                                <option value="entrada" @selected(old('tipo') === 'entrada')>Entrada</option>
                                <option value="salida" @selected(old('tipo') === 'salida')>Salida</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cantidad</label>
                            <input type="number" name="cantidad" id="movimiento-cantidad" value="{{ old('cantidad', 1) }}" min="1" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Motivo (opcional)</label>
                        <input type="text" name="motivo" id="movimiento-motivo" value="{{ old('motivo') }}" placeholder="Ej: Compra a proveedor / Venta de mostrador" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                        <button type="button" onclick="toggleMovimientoModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition shadow">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function toggleMovimientoModal() {
                document.getElementById('modalMovimiento').classList.toggle('hidden');
            }

            function openMovimientoModal(boton) {
                const datos = boton.dataset;

                document.getElementById('movimiento-producto-id').value = datos.id;
                document.getElementById('movimiento-producto-nombre').textContent = datos.codigo + ' — ' + datos.descripcion;
                document.getElementById('movimiento-producto-stock').textContent = datos.stock;

                const form = document.getElementById('formMovimiento');
                form.action = form.action.replace('__ID__', datos.id);

                toggleMovimientoModal();
            }

            @if ($errors->any() && old('producto_id'))
                document.addEventListener('DOMContentLoaded', function () {
                    const boton = document.querySelector(
                        'button[onclick="openMovimientoModal(this)"][data-id="{{ old('producto_id') }}"]'
                    );

                    if (boton) {
                        openMovimientoModal(boton);
                    }
                });
            @endif
        </script>
    @endif
@endsection
