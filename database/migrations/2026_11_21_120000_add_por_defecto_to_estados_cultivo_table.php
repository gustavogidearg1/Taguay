<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estados_cultivo', function (Blueprint $table) {
            // Indica cuál es el estado que se preselecciona en la Carga Rápida.
            $table->boolean('por_defecto')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('estados_cultivo', function (Blueprint $table) {
            $table->dropColumn('por_defecto');
        });
    }
};
