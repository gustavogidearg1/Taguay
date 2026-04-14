<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            $table->decimal('produccion_tn', 12, 2)->nullable()->after('observacion');
            $table->decimal('superficie_ha', 12, 2)->nullable()->after('produccion_tn');
            $table->decimal('rinde', 12, 2)->nullable()->after('superficie_ha');

            $table->string('imagen_1', 255)->nullable()->after('rinde');
            $table->string('imagen_2', 255)->nullable()->after('imagen_1');

            $table->string('chasis', 100)->nullable()->after('imagen_2');
            $table->string('chofer', 150)->nullable()->after('chasis');

            $table->boolean('pertenece_empresa')->default(false)->after('chofer');

            $table->decimal('lluvia', 10, 2)->nullable()->after('pertenece_empresa');
            $table->decimal('humedad', 10, 2)->nullable()->after('lluvia');

            $table->boolean('silo')->default(false)->after('humedad');

            $table->string('numero', 100)->nullable()->after('silo');
        });
    }

    public function down(): void
    {
        Schema::table('lote_estados', function (Blueprint $table) {
            $table->dropColumn([
                'produccion_tn',
                'superficie_ha',
                'rinde',
                'imagen_1',
                'imagen_2',
                'chasis',
                'chofer',
                'pertenece_empresa',
                'lluvia',
                'humedad',
                'silo',
                'numero',
            ]);
        });
    }
};
