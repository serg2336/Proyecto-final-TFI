@extends('layouts.app')

@section('titulo_pagina', 'Pedidos a Proveedores')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Pedidos de Reposición</h3>
            <p class="text-gray-500 text-sm">Gestioná las compras a tus proveedores.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>➕</span> Nuevo Pedido
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Nº Pedido</th>
                    <th class="py-4 px-6 border-b">Proveedor</th>
                    <th class="py-4 px-6 border-b">Fecha</th>
                    <th class="py-4 px-6 border-b">Total</th>
                    <th class="py-4 px-6 border-b">Estado</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">PED-001</td>
                    <td class="py-4 px-6 font-semibold">Repuestos del Norte S.A.</td>
                    <td class="py-4 px-6">01/11/2024</td>
                    <td class="py-4 px-6">$ 150,000.00</td>
                    <td class="py-4 px-6"><span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">Recibido</span></td>
                </tr>
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-4 px-6 font-mono text-sm">PED-002</td>
                    <td class="py-4 px-6 font-semibold">Distribuidora Fiat</td>
                    <td class="py-4 px-6">03/11/2024</td>
                    <td class="py-4 px-6">$ 85,000.00</td>
                    <td class="py-4 px-6"><span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs font-bold">Pendiente</span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection