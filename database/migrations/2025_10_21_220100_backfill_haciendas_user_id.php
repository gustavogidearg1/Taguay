<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiá este ID por el usuario que quieras asignar como creador histórico
        $asignarA = 5;
        DB::table('haciendas')->whereNull('user_id')->update(['user_id' => $asignarA]);
    }

    public function down(): void
    {
        // Revertir: dejar nuevamente en NULL
        DB::table('haciendas')->update(['user_id' => null]);
    }
};
