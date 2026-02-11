<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->string('licencia_nro')->unique();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->onDelete('set null');
            $table->enum('estado', ['disponible', 'ocupado', 'fuera'])->default('fuera');
            $table->decimal('ubicacion_lat', 10, 8)->nullable();
            $table->decimal('ubicacion_lng', 11, 8)->nullable();
            $table->decimal('calificacion', 3, 1)->default(5);
            $table->string('municipio')->default('Arrecife');
            $table->timestamps();
            
            $table->index('usuario_id');
            $table->index('estado');
            $table->index('municipio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxistas');
    }
};
