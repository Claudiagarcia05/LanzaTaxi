<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->nullable()->constrained('viajes')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('tipo', ['pago', 'comisión', 'reembolso']);
            $table->decimal('monto', 10, 2);
            $table->enum('metodo', ['efectivo', 'tarjeta', 'bizum', 'wallet']);
            $table->enum('estado', ['pendiente', 'completado', 'fallido'])->default('pendiente');
            $table->string('referencia')->nullable();
            $table->timestamps();
            
            $table->index('viaje_id');
            $table->index('usuario_id');
            $table->index('tipo');
            $table->index('estado');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
