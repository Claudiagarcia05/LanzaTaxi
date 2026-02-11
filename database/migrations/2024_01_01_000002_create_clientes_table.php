<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->nullable();
            $table->string('metodo_pago')->default('tarjeta');
            $table->timestamps();
            
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
