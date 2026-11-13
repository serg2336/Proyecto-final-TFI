<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    use HasFactory;

    public const TIPO_ENTRADA = 'entrada';

    public const TIPO_SALIDA = 'salida';

    protected $fillable = [
        'producto_id', 'tipo', 'cantidad', 'motivo',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
