<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teléfono de WhatsApp del usuario, solo dígitos con código de país (ej.: 5493534191741).
 * Con él, el bot de WhatsApp sabe quién está cargando el Avance de Campo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono_whatsapp', 20)->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['telefono_whatsapp']);
            $table->dropColumn('telefono_whatsapp');
        });
    }
};
