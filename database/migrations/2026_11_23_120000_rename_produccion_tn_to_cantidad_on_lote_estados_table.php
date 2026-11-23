<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La producción pasa a cargarse en kilogramos: la columna produccion_tn se renombra a cantidad.
 * No se convierten valores (al momento del cambio no había cargas reales).
 * El rinde se sigue expresando en Tn/ha: se calcula como (cantidad ÷ 1000) ÷ superficie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            $table->renameColumn('produccion_tn', 'cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            $table->renameColumn('cantidad', 'produccion_tn');
        });
    }
};
