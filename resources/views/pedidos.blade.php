@extends('layouts.app')

@section('titulo_pagina', 'Pedidos a Proveedores')

@section('content')
    @php
        $estados = [
            'pendiente' => ['label' => 'Pendiente', 'clase' => 'text-yellow-600 bg-yellow-100'],
            'recibido' => ['label' => 'Recibido', 'clase' => 'text-green-600 bg-green-100'],
            'cancelado' => ['label' => 'Cancelado', 'clase' => 'text-red-600 bg-red-100'],
        ];

        $buscar = trim((string) request('buscar', ''));
        $estadoFiltro = (string) request('estado', '');
        $proveedorFiltro = (string) request('proveedor', '');

        $pedidosFiltrados = $pedidos
            ->when($buscar !== '', fn ($coleccion) => $coleccion->filter(function ($pedido) use ($buscar) {
                $texto = mb_strtolower(
                    ($pedido->producto?->codigo ?? '').' '.
                    ($pedido->producto?->descripcion ?? '').' '.
                    ($pedido->proveedor ?? '')
                );

                return str_contains($texto, mb_strtolower($buscar));
            }))
            ->when($estadoFiltro !== '', fn ($coleccion) => $coleccion->filter(
                fn ($pedido) => $pedido->estado === $estadoFiltro
            ))
            ->when($proveedorFiltro !== '', fn ($coleccion) => $coleccion->filter(
                fn ($pedido) => ($pedido->proveedor ?? '') === $proveedorFiltro
            ))
            ->values();

        $proveedores = $pedidos->pluck('proveedor')->filter()->unique()->sort()->values();

        $totalPedidos = $pedidos->count();
        $pendientes = $pedidos->where('estado', 'pendiente')->count();
        $recibidos = $pedidos->where('estado', 'recibido')->count();
        $unidadesPedidas = $pedidos->where('estado', 'pendiente')->sum('cantidad');
    @endphp

    <!-- Encabezado -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Pedidos de reposición</h3>
            <p class="text-gray-500 text-sm">Gestioná las compras a proveedores y el ingreso de mercadería al stock.</p>
        </div>
        <button
            type="button"
            onclick="toggleModal('modalNuevoPedido')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2"
        >
            <span aria-hidden="true">➕</span> Nuevo pedido
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <!-- Indicadores -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pedidos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPedidos }}</p>
            <p class="text-xs text-gray-400 mt-1">Registrados en total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pendientes</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $pendientes }}</p>
            <p class="text-xs text-gray-400 mt-1">Esperando recepción</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Recibidos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $recibidos }}</p>
            <p class="text-xs text-gray-400 mt-1">Ya sumaron stock</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unidades pedidas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($unidadesPedidas, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">En pedidos pendientes</p>
        </div>
    </div>

    <!-- Búsqueda y filtros -->
    <form method="GET" action="{{ route('pedidos.index') }}" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4">
        <input
            type="search"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Buscar por producto, código o proveedor..."
            class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        <select name="proveedor" class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Todos los proveedores</option>
            @foreach ($proveedores as $proveedor)
                <option value="{{ $proveedor }}" @selected($proveedorFiltro === $proveedor)>{{ $proveedor }}</option>
            @endforeach
        </select>

        <select name="estado" class="w-full md:w-1/5 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Todos los estados</option>
            @foreach ($estados as $clave => $estado)
                <option value="{{ $clave }}" @selected($estadoFiltro === $clave)>{{ $estado['label'] }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Filtrar
        </button>

        @if ($buscar !== '' || $estadoFiltro !== '' || $proveedorFiltro !== '')
            <a href="{{ route('pedidos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center underline">
                Limpiar filtros
            </a>
        @endif
    </form>

    <!-- Tabla de pedidos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 border-b">Nº Pedido</th>
                        <th class="py-4 px-6 border-b">Producto</th>
                        <th class="py-4 px-6 border-b">Proveedor</th>
                        <th class="py-4 px-6 border-b text-center">Cantidad</th>
                        <th class="py-4 px-6 border-b">Fecha</th>
                        <th class="py-4 px-6 border-b">Estado</th>
                        <th class="py-4 px-6 border-b text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($pedidosFiltrados as $pedido)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="py-4 px-6 font-mono text-sm">
                                PED-{{ str_pad($pedido->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-gray-800">{{ $pedido->producto?->descripcion ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $pedido->producto?->codigo ?? '—' }}
                                    · Stock actual: {{ $pedido->producto?->stock ?? 0 }} un.
                                </p>
                            </td>
                            <td class="py-4 px-6">{{ $pedido->proveedor ?: 'Sin proveedor' }}</td>
                            <td class="py-4 px-6 text-center font-semibold">{{ $pedido->cantidad }} un.</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $pedido->created_at->locale('es')->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="{{ $estados[$pedido->estado]['clase'] ?? 'text-gray-600 bg-gray-100' }} px-2 py-1 rounded text-xs font-bold">
                                    {{ $estados[$pedido->estado]['label'] ?? ucfirst($pedido->estado) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($pedido->estado === 'pendiente')
                                    <form
                                        action="{{ route('pedidos.recibir', $pedido) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('¿Recibir el pedido PED-{{ str_pad($pedido->id, 3, '0', STR_PAD_LEFT) }}? Se sumarán {{ $pedido->cantidad }} unidades al stock de {{ $pedido->producto?->descripcion }}.')"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded shadow transition">
                                            Recibir pedido
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">
                                        {{ $pedido->estado === 'recibido' ? 'Recibido el '.$pedido->updated_at->locale('es')->format('d/m/Y') : '—' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 px-6 text-center text-gray-500">
                                @if ($buscar !== '' || $estadoFiltro !== '' || $proveedorFiltro !== '')
                                    <p class="font-semibold text-gray-700">No se encontraron pedidos</p>
                                    <p class="text-sm">Probá con otro término o revisá los filtros aplicados.</p>
                                @else
                                    <p class="font-semibold text-gray-700">Todavía no hay pedidos</p>
                                    <p class="text-sm">Registrá el primero con el botón «Nuevo pedido».</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
            <span class="text-sm text-gray-600">Mostrando {{ $pedidosFiltrados->count() }} pedido(s)</span>
            <span class="text-xs text-gray-400">Al recibir un pedido, la cantidad se suma al stock del producto.</span>
        </div>
    </div>

    <!-- MODAL: NUEVO PEDIDO -->
    <div
        id="modalNuevoPedido"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50"
        role="dialog"
        aria-modal="true"
        aria-labelledby="tituloNuevoPedido"
    >
        <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <div>
                    <h3 id="tituloNuevoPedido" class="text-xl font-bold text-gray-800">Nuevo pedido a proveedor</h3>
                    <p class="text-sm text-gray-500">Elegí el repuesto y la cantidad que necesitás reponer.</p>
                </div>
                <button type="button" onclick="toggleModal('modalNuevoPedido')" class="text-gray-500 hover:text-red-500 text-2xl font-bold" aria-label="Cerrar">&times;</button>
            </div>

            <form action="{{ route('pedidos.store') }}" method="POST" class="mt-4">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="pedido-producto" class="block text-sm font-semibold text-gray-700 mb-1">Repuesto</label>
                        <select id="pedido-producto" name="producto_id" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                            <option value="">Seleccioná un repuesto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" @selected((string) old('producto_id') === (string) $producto->id)>
                                    {{ $producto->codigo }} — {{ $producto->descripcion }} (stock: {{ $producto->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="pedido-cantidad" class="block text-sm font-semibold text-gray-700 mb-1">Cantidad</label>
                        <input type="number" id="pedido-cantidad" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="pedido-proveedor" class="block text-sm font-semibold text-gray-700 mb-1">Proveedor (opcional)</label>
                        <input type="text" id="pedido-proveedor" name="proveedor" value="{{ old('proveedor') }}" list="listaProveedores" placeholder="Ej: Repuestos del Norte S.A." class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                        <datalist id="listaProveedores">
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                    <button type="button" onclick="toggleModal('modalNuevoPedido')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition shadow">
                        Guardar pedido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.toggle('hidden');

            if (!modal.classList.contains('hidden')) {
                const primero = modal.querySelector('input, select, button');
                if (primero) primero.focus();
            }
        }

        // Si la validación del backend falla, reabrimos el modal con los errores.
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modalNuevoPedido').classList.remove('hidden');
            });
        @endif
    </script>
@endsection
