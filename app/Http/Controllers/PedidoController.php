<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Listar todos los pedidos
    public function index()
    {
        $pedidos = Pedido::with('producto')
            ->orderByRaw("estado = 'pendiente' desc")
            ->orderByDesc('created_at')
            ->get(); // Trae los pedidos con los datos del producto

        // Productos disponibles para cargar un pedido nuevo desde el modal.
        $productos = Producto::orderBy('descripcion')->get();

        return view('pedidos', compact('pedidos', 'productos'));
    }

    // Guardar un pedido nuevo
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Creamos el pedido
        Pedido::create([
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'proveedor' => $request->proveedor,
            'estado' => 'pendiente'
        ]);

        return redirect()->back()->with('success', 'Pedido a proveedor registrado exitosamente.');
    }

    // Marcar pedido como RECIBIDO y sumar el stock
    public function recibir(Pedido $pedido)
    {
        if ($pedido->estado === 'pendiente') {
            // 1. Sumamos la cantidad al stock del producto
            $producto = $pedido->producto;
            $producto->stock = $producto->stock + $pedido->cantidad;
            $producto->save();

            // 2. Cambiamos el estado del pedido
            $pedido->estado = 'recibido';
            $pedido->save();
        }

        return redirect()->back()->with('success', 'Pedido recibido y stock actualizado.');
    }
}