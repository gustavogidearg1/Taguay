<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id(); // id
            $table->string('nombre', 200);
            $table->string('ubicacion', 255)->nullable(); // ubicación libre
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
