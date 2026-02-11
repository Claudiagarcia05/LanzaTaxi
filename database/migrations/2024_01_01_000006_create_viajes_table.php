<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('taxista_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->decimal('origen_lat', 10, 8);
            $table->decimal('origen_lng', 11, 8);
            $table->string('origen_direccion');
            $table->decimal('destino_lat', 10, 8);
            $table->decimal('destino_lng', 11, 8);
            $table->string('destino_direccion');
            $table->decimal('distancia_km', 8, 2);
            $table->integer('tiempo_estimado');
            $table->decimal('precio', 8, 2);
            $table->foreignId('tarifa_id')->constrained('tarifas')->onDelete('restrict');
            $table->enum('estado', ['solicitado', 'aceptado', 'en_curso', 'completado', 'cancelado'])->default('solicitado');
            $table->integer('ocupantes')->default(1);
            $table->text('comentarios')->nullable();
            $table->json('calificaciones')->nullable();
            $table->string('metodo_pago')->default('tarjeta');
            $table->boolean('pagado')->default(false);
            $table->timestamps();
            
            $table->index('cliente_id');
            $table->index('taxista_id');
            $table->index('estado');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
