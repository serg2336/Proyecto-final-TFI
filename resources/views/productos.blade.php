@extends('layouts.app')

@section('titulo_pagina', 'Gestión de Productos (Repuestos)')

@section('content')
    <!-- Encabezado con botón de crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Catálogo de Repuestos</h3>
            <p class="text-gray-500 text-sm">Administrá el stock, precios y marcas de tus productos.</p>
        </div>
        <button onclick="toggleModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>➕</span> Nuevo Producto
        </button>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 flex gap-4">
        <input type="text" placeholder="Buscar por código o descripción..." class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        
        <select class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option>Todas las Marcas</option>
            <option>Fiat</option>
            <option>Volkswagen</option>
            <option>Ford</option>
        </select>

        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Filtrar
        </button>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Código</th>
                    <th class="py-4 px-6 border-b">Descripción</th>
                    <th class="py-4 px-6 border-b">Marca / Modelo</th>
                    <th class="py-4 px-6 border-b">Precio Venta</th>
                    <th class="py-4 px-6 border-b">Stock</th>
                    <th class="py-4 px-6 border-b text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <!-- Fila 1 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">REP-001</td>
                    <td class="py-4 px-6 font-semibold">Filtro de Aceite</td>
                    <td class="py-4 px-6">Fiat / Palio</td>
                    <td class="py-4 px-6">$ 12,500.00</td>
                    <td class="py-4 px-6">
                        <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">25 un.</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-red-500 hover:text-red-700 font-semibold text-sm">Eliminar</button>
                    </td>
                </tr>
                <!-- Fila 2 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">REP-002</td>
                    <td class="py-4 px-6 font-semibold">Pastillas de Freno</td>
                    <td class="py-4 px-6">Fiat / Uno</td>
                    <td class="py-4 px-6">$ 18,200.00</td>
                    <td class="py-4 px-6">
                        <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs font-bold">2 un.</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-red-500 hover:text-red-700 font-semibold text-sm">Eliminar</button>
                    </td>
                </tr>
                <!-- Fila 3 -->
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 font-mono text-sm">REP-003</td>
                    <td class="py-4 px-6 font-semibold">Correa Poly-V</td>
                    <td class="py-4 px-6">Fiat / Siena</td>
                    <td class="py-4 px-6">$ 8,900.00</td>
                    <td class="py-4 px-6">
                        <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs font-bold">5 un.</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-red-500 hover:text-red-700 font-semibold text-sm">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Paginación (visual) -->
        <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
            <span class="text-sm text-gray-600">Mostrando 1 a 3 de 1,245 productos</span>
            <div class="flex space-x-1">
                <button class="px-3 py-1 border border-gray-300 rounded bg-white text-gray-500 hover:bg-gray-100">Anterior</button>
                <button class="px-3 py-1 border border-gray-300 rounded bg-blue-500 text-white">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded bg-white text-gray-500 hover:bg-gray-100">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded bg-white text-gray-500 hover:bg-gray-100">Siguiente</button>
            </div>
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
            <form action="#" method="POST" class="mt-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Código del Repuesto</label>
                        <input type="text" placeholder="Ej: REP-004" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                        <input type="text" placeholder="Ej: Filtro de Aire" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Marca</label>
                        <select class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                            <option>Fiat</option>
                            <option>Volkswagen</option>
                            <option>Ford</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Modelo Compatible</label>
                        <input type="text" placeholder="Ej: Palio / Siena" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Precio de Venta ($)</label>
                        <input type="number" placeholder="0.00" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Inicial</label>
                        <input type="number" placeholder="Ej: 10" class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none" required>
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
    </script>
@endsection