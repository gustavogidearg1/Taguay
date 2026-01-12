<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 0) Asegurar que exista el rol 4 (Invitado) por si hay NULLs que vamos a setear a 4
        if (!DB::table('roles')->where('id', 4)->exists()) {
            DB::table('roles')->insert([
                'id' => 4,
                'name' => 'Invitado',
                'description' => 'Vera informes limitados',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 1) Si existe una FK previa, eliminarla (nombre t¨ªpico de Laravel)
        //    Si tu hosting gener¨® un nombre distinto, pod¨¦s agregar un try/catch o consultar information_schema.
        Schema::table('users', function (Blueprint $table) {
            // Evitar excepci¨®n si no existe la FK
            try {
                $table->dropForeign(['role_id']); // drops users_role_id_foreign
            } catch (\Throwable $e) { /* ignorar */ }
        });

        // 2) Asegurar la columna role_id exista; si no existe, crearla primero como nullable
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('password');
            });
        }

        // 3) Reemplazar NULLs por 4 (para poder volver NOT NULL)
        DB::table('users')->whereNull('role_id')->update(['role_id' => 4]);

        // 4) Volver la columna NOT NULL + default 4
        //    OJO: para usar ->change() se requiere doctrine/dbal. Si no lo ten¨¦s en el hosting,
        //    us¨¢ un ALTER crudo.
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->default(4)->nullable(false)->change();
            });
        } catch (\Throwable $e) {
            // Fallback sin DBAL (MySQL)
            DB::statement("ALTER TABLE `users` MODIFY `role_id` BIGINT UNSIGNED NOT NULL DEFAULT 4");
        }

        // 5) Crear FK con ON DELETE RESTRICT (o NO ACTION). Ya no usamos SET NULL.
        Schema::table('users', function (Blueprint $table) {
            // Por las dudas, crear ¨ªndice si no lo hay (MySQL lo crea al agregar FK, pero ser expl¨ªcitos ayuda)
            $table->index('role_id', 'users_role_id_index');
            $table->foreign('role_id', 'users_role_id_foreign')
                  ->references('id')->on('roles')
                  ->onDelete('restrict')   // o ->restrictOnDelete()
                  ->onUpdate('cascade');   // typical
        });
    }

    public function down(): void
    {
        // Revertir cambios con cuidado (no borrar datos)
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign('users_role_id_foreign');
            } catch (\Throwable $e) { /* ignorar */ }

            try {
                $table->dropIndex('users_role_id_index');
            } catch (\Throwable $e) { /* ignorar */ }
        });

        // Si quer¨¦s volver a nullable (opcional)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->default(null)->change();
            });
        } catch (\Throwable $e) {
            DB::statement("ALTER TABLE `users` MODIFY `role_id` BIGINT UNSIGNED NULL DEFAULT NULL");
        }
    }
};
