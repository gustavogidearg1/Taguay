<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);                 // Ej: Trigo Pan
            $table->string('codfinneg', 30)->unique();   // Ej: TP
            $table->boolean('filtro_power_bi')->default(false); // Si/No
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};

