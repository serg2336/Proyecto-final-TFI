<?php

namespace App\Exports;

use App\Models\Movimiento;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private Collection $movimientos) {}

    public function collection(): Collection
    {
        return $this->movimientos->map(function (Movimiento $movimiento) {
            return [
                $movimiento->created_at->format('d/m/Y H:i'),
                $movimiento->producto?->codigo ?? '—',
                $movimiento->producto?->descripcion ?? 'Producto eliminado',
                $movimiento->tipo === Movimiento::TIPO_ENTRADA ? 'Entrada' : 'Salida',
                (int) $movimiento->cantidad,
                $movimiento->motivo ?? '—',
            ];
        });
    }

    public function headings(): array
    {
        return ['Fecha', 'Código', 'Producto', 'Tipo', 'Cantidad', 'Motivo'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Movimientos';
    }
}
