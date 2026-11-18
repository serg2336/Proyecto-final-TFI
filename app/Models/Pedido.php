<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id', 'cantidad', 'proveedor', 'estado'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}