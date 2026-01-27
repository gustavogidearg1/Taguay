<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('condicion_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('codigo', 50)->unique();

            $table->unsignedInteger('div_mes')->nullable();  // ej: 3
            $table->unsignedInteger('num_dias')->nullable(); // ej: 210

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condicion_pagos');
    }
};
