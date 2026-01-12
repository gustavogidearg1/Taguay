<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hacienda extends Model
{
    protected $table = 'haciendas';

    // id_Hacienda = id (bigIncrements)
    protected $fillable = [
        'cliente',
        'consignatario',
        'vendedor',
        'categoria_id',
        'cantidad',
        'transportista',
        'patente',
        'establecimiento_id',
        'destino',
        'peso_vivo_menos_8',
        'user_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }
    
    protected $appends = ['subtotal_peso_vivo']; // opcional, para json/arrays
    
        public function getSubtotalPesoVivoAttribute()
    {
        $peso  = (float) ($this->peso_vivo_menos_8 ?? 0);
        $cant  = (float) ($this->cantidad ?? 0);
        return round($peso * $cant, 1); // 1 decimal como el resto
    }
    
    
    public function user()
{
    return $this->belongsTo(User::class);
}


}
