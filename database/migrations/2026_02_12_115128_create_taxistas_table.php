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
        Schema::create('taxistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('licencia')->unique();
            $table->string('municipio');
            $table->string('matricula');
            $table->string('modelo_vehiculo')->nullable();
            $table->enum('estado', ['libre', 'ocupado', 'en_servicio'])->default('ocupado');
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->decimal('valoracion_media', 3, 2)->default(0);
            $table->integer('num_valoraciones')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxistas');
    }
};
