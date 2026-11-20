<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar que venga un array de productos
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $totalVenta = 0;

        // 2. Crear la venta general (primero con total 0)
        $venta = Venta::create([
            'user_id' => auth()->id(),
            'total' => 0,
        ]);

        // 3. Recorrer el carrito y guardar cada producto
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['id']);
            
            // Verificar stock
            if ($producto->stock < $item['cantidad']) {
                // Si falla uno, borramos la venta que acabamos de crear y volvemos
                $venta->delete(); 
                return redirect()->back()->with('error', "No hay stock suficiente de {$producto->descripcion}.");
            }

            $subtotal = $producto->precio * $item['cantidad'];
            $totalVenta += $subtotal;

            // Guardar en la tabla de detalles
            $venta->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio,
            ]);

            // Descontar stock
            $producto->stock -= $item['cantidad'];
            $producto->save();
        }

        // 4. Actualizar el total real de la venta
        $venta->update(['total' => $totalVenta]);

        // 5. Volver con mensaje de éxito
        return redirect()->route('ventas.index')->with('success', '¡Venta registrada con éxito!');
    }
}