<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear tabla solo si NO existe
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable(); // <- alineado a tu DB
                $table->timestamps();
            });
        } else {
            // 2) Si existe, asegurar columna 'description'
            if (!Schema::hasColumn('roles', 'description')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('name');
                });
            }
        }

        // 3) Insertar roles si no existen (idempotente)
        $base = [
            ['name' => 'Super Admin', 'description' => 'El Administrador podra realizar todos los cambios que desee'],
            ['name' => 'Admin',       'description' => 'El editor podra realizar algunos cambios, editar y agregar, segun se especifique.'],
            ['name' => 'Editor',      'description' => 'El usuario, solo podra visualziar, pero no podra agregar o editar.'],
            ['name' => 'Invitado',    'description' => 'Vera informes limitados'],
        ];

        foreach ($base as $row) {
            $exists = DB::table('roles')->where('name', $row['name'])->exists();
            if (!$exists) {
                DB::table('roles')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Si la tabla la creaste con esta migración, se podría dropear.
        // Pero como ya existe en tu hosting, es más seguro NO borrarla
        // para no perder datos. Dejamos vacío.
        // Schema::dropIfExists('roles');
    }
};
