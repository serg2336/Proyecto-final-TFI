@extends('layouts.app')

@section('titulo_pagina', 'Registro de Ventas')

@section('content')
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Historial de Ventas</h3>
            <p class="text-gray-500 text-sm">Registrá y consultá las ventas de repuestos.</p>
        </div>
        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>💰</span> Registrar Nueva Venta
        </button>
    </div>

    <!-- Barra de Búsqueda y Filtro -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 flex gap-4">
        <input type="text" placeholder="Buscar ticket o cliente..." class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-500">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Filtrar
        </button>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Nº Ticket</th>
                    <th class="py-4 px-6 border-b">Fecha y Hora</th>
                    <th class="py-4 px-6 border-b">Cliente</th>
                    <th class="py-4 px-6 border-b">Método de Pago</th>
                    <th class="py-4 px-6 border-b">Total</th>
                    <th class="py-4 px-6 border-b text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <!-- Fila 1 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">T-00125</td>
                    <td class="py-4 px-6">Hoy, 11:45 hs</td>
                    <td class="py-4 px-6 font-semibold">Juan Pérez</td>
                    <td class="py-4 px-6"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Efectivo</span></td>
                    <td class="py-4 px-6 font-bold">$ 12,500.00</td>
                    <td class="py-4 px-6 text-center">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Ver Ticket</button>
                    </td>
                </tr>
                <!-- Fila 2 -->
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">T-00124</td>
                    <td class="py-4 px-6">Hoy, 10:15 hs</td>
                    <td class="py-4 px-6 font-semibold">Taller Mecánico "El Rápido"</td>
                    <td class="py-4 px-6"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">Cta. Corriente</span></td>
                    <td class="py-4 px-6 font-bold">$ 45,000.00</td>
                    <td class="py-4 px-6 text-center">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Ver Ticket</button>
                    </td>
                </tr>
                <!-- Fila 3 -->
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 font-mono text-sm">T-00123</td>
                    <td class="py-4 px-6">Ayer, 16:30 hs</td>
                    <td class="py-4 px-6 font-semibold">Transporte del Sur S.R.L.</td>
                    <td class="py-4 px-6"><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Transferencia</span></td>
                    <td class="py-4 px-6 font-bold">$ 128,900.00</td>
                    <td class="py-4 px-6 text-center">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Ver Ticket</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection