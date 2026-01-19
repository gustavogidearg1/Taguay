<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('nro_contrato');      // 5428001
            $table->unsignedBigInteger('num_forward')->nullable();

            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));

            $table->date('entrega_inicial')->nullable();
            $table->date('entrega_final')->nullable();

            $table->foreignId('campania_id')->constrained('campanias');
            $table->foreignId('cultivo_id')->constrained('cultivos');
            $table->foreignId('moneda_id')->constrained('monedas');

            // Selects (guardados como texto abreviado)
            $table->string('caracteristica_precio', 20)->default('PRECIO_HECHO'); // Precio hecho, A fijar, Condicional
            $table->string('formacion_precio', 20)->default('A_COBRAR');          // A cobrar, Con Anticipo, En Canje, Forward
            $table->string('condicion_precio', 20)->default('ENTREGA_OBL');       // Entrega obligatoria, Washout
            $table->string('condicion_pago', 20)->default('A_COBRAR');            // A cobrar, Con Anticipo, En Canje
            $table->string('lista_grano', 10)->default('ABIERTA');                // Abierta, Cerrada

            // Cliente desde API (guardamos ambos)
            $table->string('cliente_codigo', 50)->nullable();
            $table->string('cliente_nombre', 255)->nullable();

            $table->string('vendedor', 120)->nullable();

            $table->string('destino', 20)->default('GRANO');                      // Grano, Otro Grano
            $table->string('formato', 20)->default('FORWARD');                    // Forward, Disponible
            $table->string('disponible_tipo', 20)->default('PRECIO_HECHO');       // Precio hecho, A Fijar

            $table->text('definicion')->nullable();

            $table->decimal('cantidad_tn', 12, 2)->nullable();
            $table->decimal('precio', 12, 2)->nullable();
            $table->decimal('precio_fijado', 12, 2)->nullable(); // (estaba repetido, lo dejamos una sola vez)

            $table->string('comision', 200)->nullable();
            $table->string('paritaria', 200)->nullable();
            $table->string('volatil', 200)->nullable();
            $table->string('obs', 200)->nullable();
            $table->string('importante', 200)->nullable();

            // Auditoría (opcional pero útil)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['nro_contrato', 'num_forward']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
