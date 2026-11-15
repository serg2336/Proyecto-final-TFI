<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. Total de dinero vendido (Suma de la columna 'total' en ventas)
        $totalVendido = Venta::sum('total') ?? 0;

        // 2. Cantidad de ventas realizadas
        $cantidadVentas = Venta::count();

        // 3. Productos más vendidos (Agrupamos las ventas por producto)
        $productosMasVendidos = Venta::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5) // Trae el Top 5
            ->with('producto') // Trae los datos del producto (nombre, etc)
            ->get();

        return view('reportes', compact('totalVendido', 'cantidadVentas', 'productosMasVendidos'));
    }
}