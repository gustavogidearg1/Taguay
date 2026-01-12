<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('haciendas', function (Blueprint $table) {
            $table->bigIncrements('id'); // id_Hacienda (alias)
            $table->string('cliente', 200);
            $table->string('consignatario', 200)->nullable();
            $table->string('vendedor', 200)->nullable();

            // Relaciones
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->decimal('cantidad', 12, 1)->default(0); // ej. 1000.0

            $table->string('transportista', 100)->nullable();
            $table->string('patente', 50)->nullable();

            $table->foreignId('establecimiento_id')
                  ->constrained('establecimientos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->string('destino', 200)->nullable();

            $table->decimal('peso_vivo_menos_8', 12, 1)->nullable(); // "Peso vivo (-8%)"

            $table->timestamps();

            // Índices útiles
            $table->index(['cliente']);
            $table->index(['categoria_id']);
            $table->index(['establecimiento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('haciendas');
    }
};
