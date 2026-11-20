<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de pedidos - ITAL FIAT</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    <div class="cabecera">
        <p class="marca">ITAL <span>FIAT</span> · Repuestos</p>
        <h1>Reporte de pedidos a proveedores</h1>
        <p class="meta">
            Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
            · Generado el {{ now()->locale('es')->format('d/m/Y H:i') }}
        </p>
        <p class="meta">
            Pedidos: {{ $resumenPedidos['total'] }}
            · Pendientes: {{ $resumenPedidos['pendientes'] }}
            · Recibidos: {{ $resumenPedidos['recibidos'] }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th class="centro">Cantidad</th>
                <th class="centro">Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pedidos as $pedido)
                <tr>
                    <td>PED-{{ str_pad($pedido->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $pedido->producto?->codigo ?? '—' }}</td>
                    <td>{{ $pedido->producto?->descripcion ?? 'Producto eliminado' }}</td>
                    <td>{{ $pedido->proveedor ?: 'Sin proveedor' }}</td>
                    <td class="centro">{{ $pedido->cantidad }}</td>
                    <td class="centro">
                        @if ($pedido->estado === 'recibido')
                            <span class="badge badge-verde">Recibido</span>
                        @elseif ($pedido->estado === 'pendiente')
                            <span class="badge badge-amarillo">Pendiente</span>
                        @else
                            <span class="badge badge-rojo">{{ ucfirst($pedido->estado) }}</span>
                        @endif
                    </td>
                    <td>{{ $pedido->created_at->locale('es')->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="sin-datos">No hay pedidos registrados en el período seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="pie">ITAL FIAT · Sistema de gestión de repuestos · Formosa, Argentina</p>
</body>
</html>
