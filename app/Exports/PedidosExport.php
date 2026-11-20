<?php

namespace App\Exports;

use App\Models\Pedido;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PedidosExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private Collection $pedidos) {}

    public function collection(): Collection
    {
        return $this->pedidos->map(function (Pedido $pedido) {
            return [
                'PED-'.str_pad((string) $pedido->id, 3, '0', STR_PAD_LEFT),
                $pedido->producto?->codigo ?? '—',
                $pedido->producto?->descripcion ?? 'Producto eliminado',
                $pedido->proveedor ?: 'Sin proveedor',
                (int) $pedido->cantidad,
                ucfirst($pedido->estado),
                $pedido->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Nº Pedido', 'Código', 'Producto', 'Proveedor', 'Cantidad', 'Estado', 'Fecha'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Pedidos';
    }
}
