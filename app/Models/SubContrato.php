<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubContrato extends Model
{
  protected $table = 'sub_contratos';

  protected $fillable = [
    'contrato_id',
    'fecha',
    'toneladas',
    'nuevo_precio_fijacion',
    'observacion',
  ];

  protected $casts = [
    'fecha' => 'date',
    'toneladas' => 'decimal:2',
    'nuevo_precio_fijacion' => 'decimal:2',
  ];

  public function contrato()
  {
    return $this->belongsTo(Contrato::class);
  }
}
