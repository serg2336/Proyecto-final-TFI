<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'producto_id', 'cantidad', 'total'
    ];

    // Relación: Una venta pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}