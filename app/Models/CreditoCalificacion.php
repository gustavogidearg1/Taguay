<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditoCalificacion extends Model
{
    protected $table = 'creditos_calificaciones';

    protected $fillable = [
        'tipo_credito',
        'financiera_id',
        'fecha',
        'vencimiento',

        'calif_total_pesos','calif_total_usd',
        'usado_total_pesos','usado_total_usd',
        'disp_total_pesos','disp_total_usd',

        'sola_firma','obs_firma',

        'prest_inm_pesos_lp_usado','prest_inm_pesos_lp_disp',
        'prest_inm_pesos_cp_usado','prest_inm_pesos_cp_disp',

        'acuerdo_descubierto_ctacte_usado','acuerdo_descubierto_ctacte_disp',

        'prest_inm_usd_lp_usado','prest_inm_usd_lp_disp',
        'prest_inm_usd_cp_usado','prest_inm_usd_cp_disp',

        'fin_galicia_nera_usd_cp_usado','fin_galicia_nera_usd_cp_disp',
        'fin_galicia_nera_pesos_cp_usado','fin_galicia_nera_pesos_cp_disp',

        'prendarios_pesos_usado','prendarios_pesos_disp',
        'prendarios_usd_usado','prendarios_usd_disp',

        'garant_sgr_pesos_usado','garant_sgr_pesos_disp',
        'garant_sgr_usd_usado','garant_sgr_usd_disp',

        'imagen',
        'observacion',
    ];

    public function financiera()
    {
        return $this->belongsTo(Financiera::class);
    }
}
