<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conversaciones en curso por WhatsApp: en qué paso de la carga está cada teléfono
 * y los datos que ya respondió. Se borra la fila al guardar o cancelar la carga.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_sesiones', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('paso', 40)->nullable();
            $table->json('datos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sesiones');
    }
};
