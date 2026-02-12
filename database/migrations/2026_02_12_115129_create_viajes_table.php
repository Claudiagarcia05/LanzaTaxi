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
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('taxista_id')->nullable()->constrained('taxistas')->onDelete('set null');
            $table->decimal('origen_lat', 10, 7);
            $table->decimal('origen_lng', 10, 7);
            $table->string('origen_direccion');
            $table->decimal('destino_lat', 10, 7);
            $table->decimal('destino_lng', 10, 7);
            $table->string('destino_direccion');
            $table->decimal('distancia', 8, 2);
            $table->decimal('precio_estimado', 8, 2);
            $table->decimal('precio_final', 8, 2)->nullable();
            $table->enum('estado', ['pendiente', 'aceptado', 'en_curso', 'finalizado', 'cancelado'])->default('pendiente');
            $table->string('tipo_tarifa');
            $table->text('suplementos')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aceptacion')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->integer('valoracion')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
