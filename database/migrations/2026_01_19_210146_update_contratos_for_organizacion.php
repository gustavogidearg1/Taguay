<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('contratos', function (Blueprint $table) {
      // 1) Agregar FK a organizaciones
      $table->foreignId('organizacion_id')
        ->nullable()
        ->after('moneda_id')
        ->constrained('organizaciones')
        ->nullOnDelete();

      // 2) Eliminar columnas viejas (cliente_codigo / cliente_nombre)
      if (Schema::hasColumn('contratos', 'cliente_codigo')) $table->dropColumn('cliente_codigo');
      if (Schema::hasColumn('contratos', 'cliente_nombre')) $table->dropColumn('cliente_nombre');
    });
  }

  public function down(): void
  {
    Schema::table('contratos', function (Blueprint $table) {
      // volver a crear cliente_* si haces rollback
      $table->string('cliente_codigo', 50)->nullable();
      $table->string('cliente_nombre', 255)->nullable();

      $table->dropForeign(['organizacion_id']);
      $table->dropColumn('organizacion_id');
    });
  }
};
