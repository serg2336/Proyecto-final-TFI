<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('cascade'); // Qué producto pedimos
            $table->integer('cantidad'); // Cuánto pedimos
            $table->string('proveedor')->nullable(); // A quién se le pidió (opcional)
            $table->string('estado')->default('pendiente'); // pendiente, recibido, cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};