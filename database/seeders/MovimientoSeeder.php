<?php

namespace Database\Seeders;

use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class MovimientoSeeder extends Seeder
{
    /**
     * Carga movimientos de demostración para los productos existentes.
     */
    public function run(): void
    {
        if (Movimiento::exists()) {
            return;
        }

        $productos = Producto::query()->orderBy('id')->get();

        foreach ($productos as $producto) {
            if ($producto->stock > 0) {
                $producto->movimientos()->create([
                    'tipo' => Movimiento::TIPO_ENTRADA,
                    'cantidad' => $producto->stock,
                    'motivo' => 'Carga inicial de stock',
                ]);
            }
        }

        $critico = $productos->first(
            fn (Producto $producto) => $producto->stock <= $producto->stock_minimo && $producto->stock > 0
        );

        if ($critico) {
            $critico->movimientos()->create([
                'tipo' => Movimiento::TIPO_SALIDA,
                'cantidad' => min(4, $critico->stock),
                'motivo' => 'Venta de mostrador',
            ]);
        }
    }
}
