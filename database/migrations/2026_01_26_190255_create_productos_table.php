<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('codigo', 50)->unique();

            $table->foreignId('unidad_id')->constrained('unidades');
            $table->foreignId('tipo_producto_id')->constrained('tipo_productos');

            $table->boolean('activo')->default(true);
            $table->boolean('stock')->default(true);
            $table->boolean('vende')->default(true);

            // numéricos (ajustá escala si necesitás)
            $table->decimal('minimo', 12, 2)->nullable();
            $table->decimal('maximo', 12, 2)->nullable();

            $table->string('obser', 200)->nullable();

            $table->timestamps();

            $table->index(['unidad_id','tipo_producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
