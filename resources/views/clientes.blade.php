@extends('layouts.app')

@section('titulo_pagina', 'Gestión de Clientes')

@section('content')
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Directorio de Clientes</h3>
            <p class="text-gray-500 text-sm">Administrá los datos de tus clientes particulares y empresas.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>➕</span> Nuevo Cliente
        </button>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 flex gap-4">
        <input type="text" placeholder="Buscar por nombre, CUIT o DNI..." class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Buscar
        </button>
    </div>

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Nombre / Razón Social</th>
                    <th class="py-4 px-6 border-b">CUIT / DNI</th>
                    <th class="py-4 px-6 border-b">Teléfono</th>
                    <th class="py-4 px-6 border-b">Saldo Cta. Cte.</th>
                    <th class="py-4 px-6 border-b text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <!-- Fila 1 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-semibold">Taller Mecánico "El Rápido"</td>
                    <td class="py-4 px-6 font-mono text-sm">30-71234567-8</td>
                    <td class="py-4 px-6">3704-123456</td>
                    <td class="py-4 px-6">
                        <span class="text-red-600 font-bold">$ 15,000.00 (Debe)</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-gray-500 hover:text-gray-700 font-semibold text-sm">Ver Historial</button>
                    </td>
                </tr>
                <!-- Fila 2 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-semibold">Juan Pérez</td>
                    <td class="py-4 px-6 font-mono text-sm">20-33117779-9</td>
                    <td class="py-4 px-6">3704-987654</td>
                    <td class="py-4 px-6">
                        <span class="text-green-600 font-bold">$ 0.00 (Al día)</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-gray-500 hover:text-gray-700 font-semibold text-sm">Ver Historial</button>
                    </td>
                </tr>
                <!-- Fila 3 -->
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 font-semibold">Transporte del Sur S.R.L.</td>
                    <td class="py-4 px-6 font-mono text-sm">30-70987654-3</td>
                    <td class="py-4 px-6">3704-555555</td>
                    <td class="py-4 px-6">
                        <span class="text-red-600 font-bold">$ 45,500.00 (Debe)</span>
                    </td>
                    <td class="py-4 px-6 text-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Editar</button>
                        <button class="text-gray-500 hover:text-gray-700 font-semibold text-sm">Ver Historial</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection