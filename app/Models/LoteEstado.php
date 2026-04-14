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
        'latitud',
        'longitud',
        'link_google_maps',
    ];

    protected $casts = [
        'fecha' => 'date',
        'produccion_tn' => 'decimal:2',
        'superficie_ha' => 'decimal:2',
        'rinde' => 'decimal:2',
        'lluvia' => 'decimal:2',
        'humedad' => 'decimal:2',
        'pertenece_empresa' => 'boolean',
        'silo' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
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

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitud},{$this->longitud}";
        }

        return $this->link_google_maps;
    }
}
