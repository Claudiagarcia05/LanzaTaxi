<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes')->onDelete('cascade');
            $table->foreignId('evaluador_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('evaluado_id')->constrained('usuarios')->onDelete('cascade');
            $table->integer('calificacion');
            $table->text('comentario')->nullable();
            $table->string('aspecto');
            $table->timestamps();
            
            $table->index('viaje_id');
            $table->index('evaluador_id');
            $table->index('evaluado_id');
            $table->unique(['viaje_id', 'evaluador_id', 'evaluado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
