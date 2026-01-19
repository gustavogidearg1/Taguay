<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
  protected $table = 'organizaciones';

  protected $fillable = [
    'codigo','name','fecha','descripcion','activo'
  ];

  protected $casts = [
    'fecha' => 'date',
    'activo' => 'boolean',
  ];

  public function contratos()
  {
    return $this->hasMany(\App\Models\Contrato::class, 'organizacion_id');
  }
}
