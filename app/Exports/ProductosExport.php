<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Producto::all();
    }

    public function headings(): array
    {
        // Asegurate de que estos nombres coincidan con las columnas de tu tabla
        return [
            'ID',
            'Descripción',
            'Stock',
            'Stock Mínimo',
            'Precio'
        ];
    }
}