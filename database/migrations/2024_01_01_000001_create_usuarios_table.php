<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->enum('tipo', ['cliente', 'taxista', 'admin'])->default('cliente');
            $table->string('avatar_url')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'baneado'])->default('activo');
            $table->boolean('verificado')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            
            $table->index('email');
            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
