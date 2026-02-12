<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('bajada_bandera', 8, 2);
            $table->decimal('precio_km', 8, 2);
            $table->decimal('suplemento_aeropuerto', 8, 2)->default(0);
            $table->decimal('suplemento_puerto', 8, 2)->default(0);
            $table->decimal('suplemento_nocturno', 8, 2)->default(0);
            $table->decimal('suplemento_festivo', 8, 2)->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
