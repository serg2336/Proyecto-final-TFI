<?php

namespace App\Http\Controllers;

use App\Exports\InventarioExport;
use App\Exports\MovimientosExport;
use App\Exports\PedidosExport;
use App\Models\Movimiento;
use App\Models\Pedido;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Panel de reportes con los datos reales del sistema.
     */
    public function index(Request $request)
    {
        $datos = $this->datos($request);

        return view('reportes', $datos);
    }

    /**
     * Reporte de inventario en PDF.
     */
    public function inventarioPdf(Request $request)
    {
        $datos = $this->datos($request);

        $pdf = Pdf::loadView('reportes.pdf.inventario', $datos)
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-inventario-'.$this->sufijo($datos).'.pdf');
    }

    /**
     * Reporte de inventario en Excel.
     */
    public function inventarioExcel(Request $request)
    {
        $datos = $this->datos($request);

        return Excel::download(
            new InventarioExport($datos['productos'], $datos['inventario']),
            'reporte-inventario-'.$this->sufijo($datos).'.xlsx'
        );
    }

    /**
     * Reporte de movimientos de stock en PDF.
     */
    public function movimientosPdf(Request $request)
    {
        $datos = $this->datos($request);

        $pdf = Pdf::loadView('reportes.pdf.movimientos', $datos)
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-movimientos-'.$this->sufijo($datos).'.pdf');
    }

    /**
     * Reporte de movimientos de stock en Excel.
     */
    public function movimientosExcel(Request $request)
    {
        $datos = $this->datos($request);

        return Excel::download(
            new MovimientosExport($datos['movimientos']),
            'reporte-movimientos-'.$this->sufijo($datos).'.xlsx'
        );
    }

    /**
     * Reporte de pedidos a proveedores en PDF.
     */
    public function pedidosPdf(Request $request)
    {
        $datos = $this->datos($request);

        $pdf = Pdf::loadView('reportes.pdf.pedidos', $datos)
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-pedidos-'.$this->sufijo($datos).'.pdf');
    }

    /**
     * Reporte de pedidos a proveedores en Excel.
     */
    public function pedidosExcel(Request $request)
    {
        $datos = $this->datos($request);

        return Excel::download(
            new PedidosExport($datos['pedidos']),
            'reporte-pedidos-'.$this->sufijo($datos).'.xlsx'
        );
    }

    /**
     * Arma todos los datos que usan la vista y las exportaciones.
     */
    private function datos(Request $request): array
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ], [
            'desde.date' => 'La fecha desde no es válida.',
            'hasta.date' => 'La fecha hasta no es válida.',
            'hasta.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
        ]);

        $desde = $request->filled('desde')
            ? Carbon::parse($request->input('desde'))->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfDay();

        $productos = Producto::orderBy('descripcion')->get();

        $inventario = [
            'productos' => $productos->count(),
            'unidades' => (int) $productos->sum('stock'),
            'valor' => (float) $productos->sum(fn (Producto $producto) => (float) $producto->precio * (int) $producto->stock),
            'bajoMinimo' => $productos->filter(
                fn (Producto $producto) => $producto->stock <= $producto->stock_minimo
            )->count(),
        ];

        $movimientos = Movimiento::with('producto')
            ->whereBetween('created_at', [$desde, $hasta])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $pedidos = Pedido::with('producto')
            ->whereBetween('created_at', [$desde, $hasta])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $resumenMovimientos = [
            'entradas' => (int) $movimientos->where('tipo', Movimiento::TIPO_ENTRADA)->sum('cantidad'),
            'salidas' => (int) $movimientos->where('tipo', Movimiento::TIPO_SALIDA)->sum('cantidad'),
            'total' => $movimientos->count(),
        ];

        $resumenPedidos = [
            'total' => $pedidos->count(),
            'pendientes' => $pedidos->where('estado', 'pendiente')->count(),
            'recibidos' => $pedidos->where('estado', 'recibido')->count(),
        ];

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'productos' => $productos,
            'inventario' => $inventario,
            'movimientos' => $movimientos,
            'resumenMovimientos' => $resumenMovimientos,
            'pedidos' => $pedidos,
            'resumenPedidos' => $resumenPedidos,
        ];
    }

    /**
     * Sufijo con el rango de fechas para el nombre de los archivos.
     */
    private function sufijo(array $datos): string
    {
        return $datos['desde']->format('Ymd').'-'.$datos['hasta']->format('Ymd');
    }
}