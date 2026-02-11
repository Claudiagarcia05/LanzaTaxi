<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxista_id')->constrained('taxistas')->onDelete('cascade');
            $table->string('marca');
            $table->string('modelo');
            $table->string('matricula')->unique();
            $table->string('color');
            $table->integer('seats')->default(4);
            $table->string('licencia')->nullable();
            $table->date('seguro_until')->nullable();
            $table->date('itv_until')->nullable();
            $table->timestamps();
            
            $table->index('taxista_id');
            $table->index('matricula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
