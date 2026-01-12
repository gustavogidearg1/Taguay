<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('haciendas', function (Blueprint $table) {
            // Agrega la columna si no existe
            if (!Schema::hasColumn('haciendas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                // Si tu tabla de usuarios NO se llama 'users', usá:
                // $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('haciendas', function (Blueprint $table) {
            if (Schema::hasColumn('haciendas', 'user_id')) {
                // Primero soltar la FK si existe (según versión de MariaDB/MySQL, una de estas funciona)
                try {
                    $table->dropConstrainedForeignId('user_id');
                } catch (\Throwable $e) {
                    // Fallback manual
                    try { $table->dropForeign(['user_id']); } catch (\Throwable $e2) {}
                    try { $table->dropColumn('user_id'); } catch (\Throwable $e3) {}
                    return;
                }
                // Luego la columna
                $table->dropColumn('user_id');
            }
        });
    }
};
