<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            // Cultivo cargado en el movimiento (viene del ABM de Cultivos)
            $table->foreignId('cultivo_id')
                ->nullable()
                ->after('estado_cultivo_id')
                ->constrained('cultivos')
                ->nullOnDelete();

            // true = propio (Taguay); false = de socio
            $table->boolean('propietario')->default(true)->after('pertenece_empresa');
        });

        // Registros anteriores: se completa el cultivo con el que tiene asignado el lote
        DB::statement('
            UPDATE lote_estados le
            JOIN lotes l ON l.id = le.lote_id
            SET le.cultivo_id = l.cultivo_id
            WHERE le.cultivo_id IS NULL AND l.cultivo_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cultivo_id');
            $table->dropColumn('propietario');
        });
    }
};
