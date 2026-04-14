<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rinde extends Model
{
    protected $fillable = [
        'lote_id',
        'user_id',
        'fecha',
        'rinde',
        'humedad',
        'superficie_cosechada',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'rinde' => 'decimal:2',
        'humedad' => 'decimal:2',
        'superficie_cosechada' => 'decimal:2',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
