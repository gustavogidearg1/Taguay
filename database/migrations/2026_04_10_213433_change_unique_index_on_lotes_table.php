<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            // elimina unique anterior: nombre + campania_id
            $table->dropUnique('lotes_nombre_campania_id_unique');

            // nuevo unique: establecimiento + campaña + nombre
            $table->unique(
                ['establecimiento_id', 'campania_id', 'nombre'],
                'lotes_establecimiento_campania_nombre_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropUnique('lotes_establecimiento_campania_nombre_unique');

            $table->unique(
                ['nombre', 'campania_id'],
                'lotes_nombre_campania_id_unique'
            );
        });
    }
};
