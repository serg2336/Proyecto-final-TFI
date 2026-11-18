@extends('layouts.app')

@section('titulo_pagina', 'Stock Actual')

@section('content')
    @php
        $puedePedir = auth()->user()?->hasAnyRole(
            \App\Models\User::ROLE_ADMIN,
            \App\Models\User::ROLE_DEPOSITO
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

                            @if ($puedePedir && $producto->stock <= $producto->stock_minimo)
                                <form
                                    action="{{ route('pedidos.store') }}"
                                    method="POST"
                                    class="mt-2 flex items-center gap-2"
                                    onsubmit="return confirm('¿Registrar el pedido al proveedor?')"
                                >
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                    <input type="hidden" name="cantidad" value="{{ max($producto->stock_minimo - $producto->stock, 1) }}">
                                    <button type="submit" class="text-green-700 bg-green-100 hover:bg-green-200 px-2 py-1 rounded text-xs font-bold transition">
                                        Pedir al proveedor
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 px-6 text-center text-gray-500">
                            <p class="font-semibold text-gray-700">Todavía no hay productos</p>
                            <p class="text-sm">Cuando agregues el primero, va a aparecer acá.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
