@extends('layouts.app')

@section('titulo_pagina', 'Reportes y Estadísticas')

@section('content')
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">Generación de Reportes</h3>
        <p class="text-gray-500 text-sm">Exportá la información del sistema para tomar decisiones.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tarjeta Reporte 1 -->
        <div class="bg-white p-6 rounded-lg shadow border-t-4 border-blue-500">
            <h4 class="text-lg font-bold text-gray-800 mb-2">Reporte de Inventario</h4>
            <p class="text-gray-500 text-sm mb-4">Listado completo de productos con su stock actual y valorización.</p>
            <div class="flex gap-2">
                <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span>📄</span> Exportar a PDF
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span>📊</span> Exportar a Excel
                </button>
            </div>
        </div>

        <!-- Tarjeta Reporte 2 -->
        <div class="bg-white p-6 rounded-lg shadow border-t-4 border-yellow-500">
            <h4 class="text-lg font-bold text-gray-800 mb-2">Reporte de Ventas</h4>
            <p class="text-gray-500 text-sm mb-4">Historial de ventas filtrado por fecha, vendedor y productos más vendidos.</p>
            <div class="flex gap-2">
                <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span>📄</span> Exportar a PDF
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2 text-sm">
                    <span>📊</span> Exportar a Excel
                </button>
            </div>
        </div>
    </div>
@endsection