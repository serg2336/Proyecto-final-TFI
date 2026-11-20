@extends('layouts.app')

@section('titulo_pagina', 'Punto de Venta')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Nueva Venta</h3>
            <p class="text-gray-500 text-sm">Agregá los productos y confirmá la venta.</p>
        </div>
    </div>

    <!-- Mensajes de error -->
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Formulario de Venta -->
    <form action="{{ route('ventas.store') }}" method="POST" id="formulario-venta">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <table class="w-full text-left border-collapse" id="tabla-venta">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 border-b w-2/5">Producto</th>
                        <th class="py-4 px-6 border-b w-24">Cantidad</th>
                        <th class="py-4 px-6 border-b w-32">Precio Unit.</th>
                        <th class="py-4 px-6 border-b w-32">Subtotal</th>
                        <th class="py-4 px-6 border-b w-16 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-factura" class="text-gray-700">
                    <!-- La primera fila la crea el sistema -->
                    <tr class="border-b fila-producto">
                        <td class="py-3 px-6">
                            <select name="productos[0][id]" class="w-full border border-gray-300 p-2 rounded select-producto" required onchange="actualizarPrecio(this)">
                                <option value="">Seleccione producto...</option>
                                @foreach(\App\Models\Producto::where('stock', '>', 0)->get() as $p)
                                    <option value="{{ $p->id }}" data-precio="{{ $p->precio }}" data-stock="{{ $p->stock }}">
                                        {{ $p->descripcion }} (Stock: {{ $p->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-3 px-6">
                            <input type="number" name="productos[0][cantidad]" min="1" value="1" class="w-full border border-gray-300 p-2 rounded input-cantidad" required onchange="calcularFila(this)">
                        </td>
                        <td class="py-3 px-6 precio-unitario-text">$0.00</td>
                        <td class="py-3 px-6 font-bold subtotal-text">$0.00</td>
                        <td class="py-3 px-6 text-center">
                            <button type="button" onclick="eliminarFila(this)" class="text-red-500 font-bold text-xl">&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                <button type="button" onclick="agregarFila()" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                    + Agregar otro producto
                </button>
            </div>
        </div>

        <!-- Total y Botón de Finalizar -->
        <div class="bg-white p-6 rounded-lg shadow flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">
                Total a Pagar: <span id="total-general" class="text-blue-600">$0.00</span>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded shadow transition text-lg">
                Confirmar y Registrar Venta
            </button>
        </div>
    </form>

    <!-- Script para la lógica de la factura -->
    <script>
        let contadorFilas = 1;

        function agregarFila() {
            const tbody = document.getElementById('cuerpo-factura');
            const nuevaFila = document.createElement('tr');
            nuevaFila.className = 'border-b fila-producto';

            // Armamos el HTML de la nueva fila usando el índice actual
            nuevaFila.innerHTML = `
                <td class="py-3 px-6">
                    <select name="productos[${contadorFilas}][id]" class="w-full border border-gray-300 p-2 rounded select-producto" required onchange="actualizarPrecio(this)">
                        <option value="">Seleccione producto...</option>
                        @foreach(\App\Models\Producto::where('stock', '>', 0)->get() as $p)
                            <option value="{{ $p->id }}" data-precio="{{ $p->precio }}" data-stock="{{ $p->stock }}">
                                {{ $p->descripcion }} (Stock: {{ $p->stock }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="py-3 px-6">
                    <input type="number" name="productos[${contadorFilas}][cantidad]" min="1" value="1" class="w-full border border-gray-300 p-2 rounded input-cantidad" required onchange="calcularFila(this)">
                </td>
                <td class="py-3 px-6 precio-unitario-text">$0.00</td>
                <td class="py-3 px-6 font-bold subtotal-text">$0.00</td>
                <td class="py-3 px-6 text-center">
                    <button type="button" onclick="eliminarFila(this)" class="text-red-500 font-bold text-xl">&times;</button>
                </td>
            `;
            
            tbody.appendChild(nuevaFila);
            contadorFilas++; // Aumentamos el índice para la próxima fila
        }

        function eliminarFila(boton) {
            const fila = boton.closest('tr');
            // Si es la única fila que queda, solo limpiamos los valores
            if (document.querySelectorAll('.fila-producto').length === 1) {
                fila.querySelector('.select-producto').value = '';
                fila.querySelector('.input-cantidad').value = 1;
                fila.querySelector('.precio-unitario-text').textContent = '$0.00';
                fila.querySelector('.subtotal-text').textContent = '$0.00';
            } else {
                // Si hay más filas, borramos la fila entera
                fila.remove();
            }
            calcularTotalGeneral();
        }

        function actualizarPrecio(select) {
            const fila = select.closest('tr');
            const opcion = select.options[select.selectedIndex];
            const precio = parseFloat(opcion.getAttribute('data-precio')) || 0;
            
            fila.querySelector('.precio-unitario-text').textContent = `$${precio.toFixed(2)}`;
            calcularFila(select); // Recalculamos el subtotal y el total general
        }

        function calcularFila(elemento) {
            const fila = elemento.closest('tr');
            const select = fila.querySelector('.select-producto');
            const cantidadInput = fila.querySelector('.input-cantidad');
            
            const opcion = select.options[select.selectedIndex];
            const precio = parseFloat(opcion.getAttribute('data-precio')) || 0;
            const stockMaximo = parseInt(opcion.getAttribute('data-stock')) || 0;
            let cantidad = parseInt(cantidadInput.value) || 1;

            // Validar stock en tiempo real
            if (cantidad > stockMaximo) {
                alert('No hay suficiente stock de este producto.');
                cantidadInput.value = stockMaximo;
                cantidad = stockMaximo;
            }

            const subtotal = precio * cantidad;
            fila.querySelector('.subtotal-text').textContent = `$${subtotal.toFixed(2)}`;

            calcularTotalGeneral();
        }

        function calcularTotalGeneral() {
            let total = 0;
            const filas = document.querySelectorAll('.fila-producto');
            
            filas.forEach(fila => {
                const subtotalTexto = fila.querySelector('.subtotal-text').textContent;
                const subtotal = parseFloat(subtotalTexto.replace('$', '')) || 0;
                total += subtotal;
            });

            document.getElementById('total-general').textContent = `$${total.toFixed(2)}`;
        }
    </script>
@endsection