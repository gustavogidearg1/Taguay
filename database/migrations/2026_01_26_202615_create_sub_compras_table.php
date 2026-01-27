<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('sub_compras', function (Blueprint $table) {
      $table->id();

      $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();

      $table->foreignId('producto_id')->constrained('productos');
      $table->decimal('cantidad', 12, 2)->default(0);

      // se guarda por línea (pero en UI se autocompleta)
      $table->foreignId('unidad_id')->constrained('unidades');

      $table->decimal('precio', 12, 2)->default(0);

      $table->foreignId('moneda_id')->constrained('monedas');
      $table->date('fecha_venc')->nullable();

      $table->decimal('bonificacion_1', 12, 4)->default(0);
      $table->decimal('bonificacion_2', 12, 4)->default(0);
      $table->decimal('bonificacion_3', 12, 4)->default(0);

      $table->decimal('sub_total', 14, 2)->default(0);

      $table->timestamps();

      $table->index(['compra_id','producto_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('sub_compras');
  }
};
