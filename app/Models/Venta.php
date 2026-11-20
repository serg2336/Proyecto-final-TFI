<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = ['total','user_id'];
    
    // ESTA ES LA RELACIÓN CORRECTA AHORA
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}