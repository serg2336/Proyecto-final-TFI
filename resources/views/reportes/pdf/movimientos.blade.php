<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de movimientos - ITAL FIAT</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    <div class="cabecera">
        <p class="marca">ITAL <span>FIAT</span> · Repuestos</p>
        <h1>Reporte de movimientos de stock</h1>
        <p class="meta">
            Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
            · Generado el {{ now()->locale('es')->format('d/m/Y H:i') }}
        </p>
        <p class="meta">
            Entradas: +{{ $resumenMovimientos['entradas'] }}
            · Salidas: -{{ $resumenMovimientos['salidas'] }}
            · Total de movimientos: {{ $resumenMovimientos['total'] }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Código</th>
                <th>Producto</th>
                <th class="centro">Tipo</th>
                <th class="centro">Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movimientos as $movimiento)
                <tr>
                    <td>{{ $movimiento->created_at->locale('es')->format('d/m/Y H:i') }}</td>
                    <td>{{ $movimiento->producto?->codigo ?? '—' }}</td>
                    <td>{{ $movimiento->producto?->descripcion ?? 'Producto eliminado' }}</td>
                    <td class="centro">
                        @if ($movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA)
                            <span class="badge badge-verde">Entrada</span>
                        @else
                            <span class="badge badge-rojo">Salida</span>
                        @endif
                    </td>
                    <td class="centro">
                        {{ $movimiento->tipo === \App\Models\Movimiento::TIPO_ENTRADA ? '+' : '-' }} {{ $movimiento->cantidad }}
                    </td>
                    <td>{{ $movimiento->motivo ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="sin-datos">No hay movimientos registrados en el período seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="pie">ITAL FIAT · Sistema de gestión de repuestos · Formosa, Argentina</p>
</body>
</html>
