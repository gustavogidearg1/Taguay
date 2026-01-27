<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCompra extends Model
{
  protected $table = 'sub_compras';

  protected $fillable = [
    'compra_id','producto_id','cantidad','unidad_id','precio',
    'moneda_id','fecha_venc',
    'bonificacion_1','bonificacion_2','bonificacion_3',
    'sub_total'
  ];

  protected $casts = [
    'cantidad' => 'decimal:2',
    'precio' => 'decimal:2',
    'bonificacion_1' => 'decimal:4',
    'bonificacion_2' => 'decimal:4',
    'bonificacion_3' => 'decimal:4',
    'sub_total' => 'decimal:2',
    'fecha_venc' => 'date',
  ];

  public function compra(){ return $this->belongsTo(Compra::class); }
  public function producto(){ return $this->belongsTo(Producto::class); }
  public function unidad(){ return $this->belongsTo(Unidad::class); }
  public function moneda(){ return $this->belongsTo(Moneda::class); }
}
