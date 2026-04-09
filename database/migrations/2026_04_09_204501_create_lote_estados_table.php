<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_estados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estado_cultivo_id')->constrained('estados_cultivo')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->date('fecha');
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_estados');
    }
};
