<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('creditos_calificaciones', function (Blueprint $table) {
            $table->id();

            $table->string('tipo_credito', 50)->nullable();

            $table->foreignId('financiera_id')
                  ->constrained('financieras')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->date('fecha')->nullable();
            $table->date('vencimiento')->nullable();

            // Totales
            $table->decimal('calif_total_pesos', 20, 2)->nullable();
            $table->decimal('calif_total_usd',   20, 2)->nullable();
            $table->decimal('usado_total_pesos', 20, 2)->nullable();
            $table->decimal('usado_total_usd',   20, 2)->nullable();
            $table->decimal('disp_total_pesos',  20, 2)->nullable();
            $table->decimal('disp_total_usd',    20, 2)->nullable();

            // Firma
            $table->decimal('sola_firma', 20, 2)->nullable();
            $table->string('obs_firma', 100)->nullable();

            // Productos / líneas
            $table->decimal('prest_inm_pesos_lp_usado',       20, 2)->nullable();
            $table->decimal('prest_inm_pesos_lp_disp',        20, 2)->nullable();
            $table->decimal('prest_inm_pesos_cp_usado',       20, 2)->nullable();
            $table->decimal('prest_inm_pesos_cp_disp',        20, 2)->nullable();

            $table->decimal('acuerdo_descubierto_ctacte_usado', 20, 2)->nullable();
            $table->decimal('acuerdo_descubierto_ctacte_disp',  20, 2)->nullable();

            $table->decimal('prest_inm_usd_lp_usado',         20, 2)->nullable();
            $table->decimal('prest_inm_usd_lp_disp',          20, 2)->nullable();
            $table->decimal('prest_inm_usd_cp_usado',         20, 2)->nullable();
            $table->decimal('prest_inm_usd_cp_disp',          20, 2)->nullable();

            $table->decimal('fin_galicia_nera_usd_cp_usado',   20, 2)->nullable();
            $table->decimal('fin_galicia_nera_usd_cp_disp',    20, 2)->nullable();
            $table->decimal('fin_galicia_nera_pesos_cp_usado', 20, 2)->nullable();
            $table->decimal('fin_galicia_nera_pesos_cp_disp',  20, 2)->nullable();

            $table->decimal('prendarios_pesos_usado',          20, 2)->nullable();
            $table->decimal('prendarios_pesos_disp',           20, 2)->nullable();
            $table->decimal('prendarios_usd_usado',            20, 2)->nullable();
            $table->decimal('prendarios_usd_disp',             20, 2)->nullable();

            $table->decimal('garant_sgr_pesos_usado',          20, 2)->nullable();
            $table->decimal('garant_sgr_pesos_disp',           20, 2)->nullable();
            $table->decimal('garant_sgr_usd_usado',            20, 2)->nullable();
            $table->decimal('garant_sgr_usd_disp',             20, 2)->nullable();

            // Imagen y observación general
            $table->string('imagen', 255)->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->index(['financiera_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creditos_calificaciones');
    }
};
