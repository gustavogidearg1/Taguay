<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $fillable = [
        'establecimiento_id',
        'campania_id',
        'cultivo_id',
        'nombre',
        'hectareas',
        'ubicacion_referencia',
        'latitud',
        'longitud',
        'link_google_maps',
    ];

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function campania()
    {
        return $this->belongsTo(Campania::class);
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class);
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitud},{$this->longitud}";
        }

        return $this->link_google_maps;
    }


    public function getGoogleMapsDirectionsUrlAttribute(): ?string
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitud},{$this->longitud}";
        }

        return null;
    }

    public function loteEstados()
{
    return $this->hasMany(\App\Models\LoteEstado::class);
}

public function rindes()
{
    return $this->hasMany(\App\Models\Rinde::class);
}


}
