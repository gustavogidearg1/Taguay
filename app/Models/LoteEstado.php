<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteEstado extends Model
{
    protected $fillable = [
        'lote_id',
        'estado_cultivo_id',
        'user_id',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function estadoCultivo()
    {
        return $this->belongsTo(EstadoCultivo::class, 'estado_cultivo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
