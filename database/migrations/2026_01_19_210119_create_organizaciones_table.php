<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('organizaciones', function (Blueprint $table) {
      $table->id();
      $table->string('codigo', 50)->unique();
      $table->string('name', 150)->unique();          // obligatorio y única (150)
      $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
      $table->text('descripcion')->nullable();
      $table->boolean('activo')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('organizaciones');
  }
};
