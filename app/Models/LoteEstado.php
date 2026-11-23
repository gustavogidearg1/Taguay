<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteEstado extends Model
{
    protected $fillable = [
        'lote_id',
        'estado_cultivo_id',
        'cultivo_id',
        'user_id',
        'fecha',
        'observacion',
        'cantidad',        // kilogramos
        'superficie_ha',
        'rinde',
        'imagen_1',
        'imagen_2',
        'chasis',
        'chofer',
        'pertenece_empresa',
        'propietario',
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
        'cantidad' => 'decimal:2',
        'superficie_ha' => 'decimal:2',
        'rinde' => 'decimal:2',
        'lluvia' => 'decimal:2',
        'humedad' => 'decimal:2',
        'pertenece_empresa' => 'boolean',
        'propietario' => 'boolean',
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

    /**
     * Cantidad en kg lista para mostrar: separador de miles y decimales solo si los tiene.
     * Ej.: 11890.00 -> "11.890" ; 56.50 -> "56,50" ; null -> "-". Uso: $registro->cantidad_formateada
     */
    public function getCantidadFormateadaAttribute(): string
    {
        if ($this->cantidad === null) {
            return '-';
        }

        $valor = (float) $this->cantidad;
        $decimales = floor($valor) == $valor ? 0 : 2;

        return number_format($valor, $decimales, ',', '.');
    }

    /** Cultivo del movimiento (puede diferir del cultivo asignado al lote). */
    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registros que puede ver el usuario: el admin ve todos, el resto solo los propios.
     */
    public function scopeVisiblesPara($query, ?User $user)
    {
        if ($user && $user->hasRole('admin')) {
            return $query;
        }

        return $query->where('user_id', $user?->id);
    }

    /** Indica si el usuario puede ver/editar este registro. */
    public function esEditablePor(?User $user): bool
    {
        return $user && ($user->hasRole('admin') || (int) $this->user_id === (int) $user->id);
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitud},{$this->longitud}";
        }

        return $this->link_google_maps;
    }
}
