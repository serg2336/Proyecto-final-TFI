<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de inventario - ITAL FIAT</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    <div class="cabecera">
        <p class="marca">ITAL <span>FIAT</span> · Repuestos</p>
        <h1>Reporte de inventario</h1>
        <p class="meta">
            Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
            · Generado el {{ now()->locale('es')->format('d/m/Y H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Marca / Modelo</th>
                <th class="derecha">Precio</th>
                <th class="centro">Stock</th>
                <th class="centro">Mínimo</th>
                <th>Estado</th>
                <th class="derecha">Valorización</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->codigo }}</td>
                    <td>{{ $producto->descripcion }}</td>
                    <td>{{ $producto->marca }}@if ($producto->modelo) / {{ $producto->modelo }}@endif</td>
                    <td class="derecha">${{ number_format((float) $producto->precio, 2, ',', '.') }}</td>
                    <td class="centro">{{ $producto->stock }}</td>
                    <td class="centro">{{ $producto->stock_minimo }}</td>
                    <td class="centro">
                        @if ($producto->stock === 0)
                            <span class="badge badge-rojo-oscuro">Sin stock</span>
                        @elseif ($producto->stock <= $producto->stock_minimo)
                            <span class="badge badge-rojo">Stock bajo</span>
                        @else
                            <span class="badge badge-verde">Disponible</span>
                        @endif
                    </td>
                    <td class="derecha">${{ number_format((float) $producto->precio * (int) $producto->stock, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="sin-datos">Todavía no hay productos cargados.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="derecha">Totales</td>
                <td class="centro">{{ $inventario['unidades'] }}</td>
                <td class="centro">—</td>
                <td class="centro">{{ $inventario['bajoMinimo'] }} bajo mínimo</td>
                <td class="derecha">${{ number_format($inventario['valor'], 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="pie">ITAL FIAT · Sistema de gestión de repuestos · Formosa, Argentina</p>
</body>
</html>
