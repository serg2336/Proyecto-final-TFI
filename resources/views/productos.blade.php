@extends('layouts.app')

@section('titulo_pagina', 'Gestión de Productos (Repuestos)')

@section('content')
    <!-- Encabezado con botón de crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Productos</h3>
            <p class="text-gray-500 text-sm">Administrá el catálogo, sus precios y disponibilidad.</p>
        </div>
        <button onclick="toggleModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>➕</span> Nuevo Producto
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Barra de Búsqueda y Filtros -->
    <form method="GET" action="{{ route('productos.index') }}" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4">
        <input
            type="search"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por código o descripción..."
            class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        <select name="marca" class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Todas las marcas</option>
            @forelse($marcas as $marca)
                <option value="{{ $marca }}" @selected(request('marca') === $marca)>{{ $marca }}</option>
            @empty
                <option value="" disabled>Sin marcas registradas</option>
            @endforelse
        </select>

        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Filtrar
        </button>

        @if (request()->filled('buscar') || request()->filled('marca'))
            <a href="{{ route('productos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center underline">
                Limpiar filtros
            </a>
        @endif
    </form>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Código</th>
                    <th class="py-4 px-6 border-b">Descripción</th>
                    <th class="py-4 px-6 border-b">Marca / Modelo</th>
                    <th class="py-4 px-6 border-b">Precio</th>
                    <th class="py-4 px-6 border-b">Stock</th>
                    <th class="py-4 px-6 border-b text-center">Acciones</th>
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
                    <td class="py-4 px-6">$ {{ number_format($producto->precio, 2, ',', '.') }}</td>
                    <td class="py-4 px-6">
                        @if($producto->stock === 0)
                            <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs font-bold" title="Este producto no tiene unidades disponibles.">Sin stock</span>
                        @elseif($producto->stock <= $producto->stock_minimo)
                            <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs font-bold" title="Quedan pocas unidades. Revisá el umbral de reposición.">Stock bajo ({{ $producto->stock }} un.)</span>
                        @else
                            <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">{{ $producto->stock }} un.</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline"
                              onsubmit="return confirm('¿Eliminar el producto {{ $producto->codigo }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 px-6 text-center text-gray-500">
                        @if (request()->filled('buscar') || request()->filled('marca'))
                            <p class="font-semibold text-gray-700">No se encontraron resultados</p>
                            <p class="text-sm">Probá con otro término o revisá los filtros aplicados.</p>
                        @else
                            <p class="font-semibold text-gray-700">Todavía no hay productos</p>
                            <p class="text-sm">Cuando agregues el primero, va a aparecer acá.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pie de tabla -->
        <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
            <span class="text-sm text-gray-600">Mostrando {{ $productos->count() }} producto(s)</span>
        </div>
    </div>

        <!-- MODAL DE NUEVO PRODUCTO (Oculto por defecto) -->
    <div id="modalNuevoProducto" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <!-- Encabezado del Modal -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">Registrar Nuevo Repuesto</h3>
                <button onclick="toggleModal()" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
            </div>

            <!-- Cuerpo del Formulario -->
            <form action="{{ route('productos.store') }}" method="POST" class="mt-4">
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
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Código del Repuesto</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: REP-004" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}" placeholder="Ej: Filtro de Aire" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Marca</label>
                        <select name="marca" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                            <option value="Fiat" @selected(old('marca') === 'Fiat')>Fiat</option>
                            <option value="Volkswagen" @selected(old('marca') === 'Volkswagen')>Volkswagen</option>
                            <option value="Ford" @selected(old('marca') === 'Ford')>Ford</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Modelo Compatible</label>
                        <input type="text" name="modelo" value="{{ old('modelo') }}" placeholder="Ej: Palio / Siena" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Precio de Venta ($)</label>
                        <input type="number" name="precio" value="{{ old('precio') }}" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Inicial</label>
                        <input type="number" name="stock" value="{{ old('stock') }}" min="0" placeholder="Ej: 10" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                </div>

                <!-- Botones del Formulario -->
                <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                    <button type="button" onclick="toggleModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition shadow">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script para abrir y cerrar el modal -->
    <script>
        function toggleModal() {
            const modal = document.getElementById('modalNuevoProducto');
            modal.classList.toggle('hidden');
        }

        // Si la validación del backend falla, reabrimos el modal con los errores
        @if ($errors->any())
            document.getElementById('modalNuevoProducto').classList.remove('hidden');
        @endif
    </script>
@endsection