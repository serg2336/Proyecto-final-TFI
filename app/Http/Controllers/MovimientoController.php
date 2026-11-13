<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientoController extends Controller
{
    /**
     * Registrar una entrada o salida de stock para un producto.
     */
    public function store(Request $request, Producto $producto): RedirectResponse
    {
        $datos = $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ], [
            'tipo.required' => 'Elegí el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'motivo.max' => 'El motivo no puede superar los 255 caracteres.',
        ]);

        $cantidad = (int) $datos['cantidad'];

        DB::transaction(function () use ($producto, $datos, $cantidad) {
            $producto = Producto::lockForUpdate()->findOrFail($producto->id);

            if ($datos['tipo'] === Movimiento::TIPO_SALIDA && $cantidad > $producto->stock) {
                throw ValidationException::withMessages([
                    'cantidad' => "No hay stock suficiente: quedan {$producto->stock} unidades.",
                ]);
            }

            $producto->movimientos()->create([
                'tipo' => $datos['tipo'],
                'cantidad' => $cantidad,
                'motivo' => $datos['motivo'] ?? null,
            ]);

            $producto->update([
                'stock' => $datos['tipo'] === Movimiento::TIPO_ENTRADA
                    ? $producto->stock + $cantidad
                    : $producto->stock - $cantidad,
            ]);
        });

        $mensaje = $datos['tipo'] === Movimiento::TIPO_ENTRADA
            ? 'Entrada de stock registrada.'
            : 'Salida de stock registrada.';

        return back()->with('success', $mensaje);
    }
}
