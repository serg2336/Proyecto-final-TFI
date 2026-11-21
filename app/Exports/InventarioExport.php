<?php

namespace App\Exports;

use App\Models\Producto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private Collection $productos,
        private array $resumen = []
    ) {}

    public function collection(): Collection
    {
        return $this->productos->map(function (Producto $producto) {
            return [
                $producto->codigo,
                $producto->descripcion,
                $producto->marca,
                $producto->modelo,
                (float) $producto->precio,
                (int) $producto->stock,
                (int) $producto->stock_minimo,
                $producto->stock === 0 ? 'Sin stock' : ($producto->stock <= $producto->stock_minimo ? 'Stock bajo' : 'Disponible'),
                (float) $producto->precio * (int) $producto->stock,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Producto',
            'Marca',
            'Modelo',
            'Precio unitario',
            'Stock',
            'Stock mínimo',
            'Estado',
            'Valorización',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '"$"#,##0.00',
            'I' => '"$"#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Inventario';
    }
}
