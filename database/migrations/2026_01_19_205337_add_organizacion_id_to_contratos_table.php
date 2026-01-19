<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('contratos', function (Blueprint $table) {
      $table->foreignId('organizacion_id')
        ->nullable()
        ->after('moneda_id')
        ->constrained('organizaciones')
        ->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('contratos', function (Blueprint $table) {
      $table->dropForeign(['organizacion_id']);
      $table->dropColumn('organizacion_id');
    });
  }
};
