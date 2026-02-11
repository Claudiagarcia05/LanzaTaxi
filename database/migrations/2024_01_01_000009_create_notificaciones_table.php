<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('viaje_id')->nullable()->constrained('viajes')->onDelete('set null');
            $table->string('tipo');
            $table->string('titulo');
            $table->text('mensaje');
            $table->boolean('leida')->default(false);
            $table->timestamps();
            
            $table->index('usuario_id');
            $table->index('leida');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
