<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('compras', function (Blueprint $table) {
      $table->id();

      $table->date('fecha');
      $table->date('fecha_entrega')->nullable();

      $table->foreignId('organizacion_id')->constrained('organizaciones');
      $table->foreignId('campania_id')->constrained('campanias');
      $table->foreignId('condicion_pago_id')->constrained('condicion_pagos');

      // "momento_pago" tipo date (ej: 30/01/2020)
      $table->date('momento_pago')->nullable();

      $table->string('codigo', 50)->unique();

      $table->foreignId('moneda_id')->constrained('monedas');
      $table->foreignId('moneda_fin_id')->nullable()->constrained('monedas');

      // ej 0.05% => 0.0005 (decimal)
      $table->decimal('tasa_financ', 12, 6)->nullable();

      $table->boolean('activo')->default(true);

      $table->string('lugar_entrega', 100)->nullable();
      $table->string('obs', 200)->nullable();

      $table->foreignId('user_id')->nullable()->constrained('users');

      $table->timestamps();

      $table->index(['organizacion_id','campania_id','condicion_pago_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('compras');
  }
};
