<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campanias', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);                // Ej: 25/26
            $table->string('codfinneg', 10)->unique(); // Ej: 25
            $table->boolean('activo')->default(true);  // por defecto activo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campanias');
    }
};

