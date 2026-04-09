<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCultivo extends Model
{
    protected $table = 'estados_cultivo';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    public function loteEstados()
    {
        return $this->hasMany(LoteEstado::class, 'estado_cultivo_id');
    }
}
