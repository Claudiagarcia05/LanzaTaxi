<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['urbano', 'interurbano', 'especial']);
            $table->decimal('bajada_bandera', 8, 2);
            $table->decimal('precio_km', 8, 2);
            $table->decimal('precio_espera_hora', 8, 2);
            $table->decimal('minimo_viaje', 8, 2);
            $table->boolean('activa')->default(true);
            $table->dateTime('vigente_desde');
            $table->dateTime('vigente_hasta')->nullable();
            $table->json('municipios')->nullable();
            $table->timestamps();
            
            $table->index('tipo');
            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
