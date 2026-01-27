<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
  protected $fillable = [
    'fecha','fecha_entrega',
    'organizacion_id','campania_id','condicion_pago_id',
    'momento_pago','codigo',
    'moneda_id','moneda_fin_id','tasa_financ',
    'activo','lugar_entrega','obs',
    'user_id'
  ];

  protected $casts = [
    'fecha' => 'date',
    'fecha_entrega' => 'date',
    'momento_pago' => 'date',
    'activo' => 'boolean',
    'tasa_financ' => 'decimal:6',
  ];

  public function organizacion(){ return $this->belongsTo(Organizacion::class); }
  public function campania(){ return $this->belongsTo(Campania::class); }
  public function condicionPago(){ return $this->belongsTo(CondicionPago::class, 'condicion_pago_id'); }

  public function moneda(){ return $this->belongsTo(Moneda::class, 'moneda_id'); }
  public function monedaFin(){ return $this->belongsTo(Moneda::class, 'moneda_fin_id'); }

  public function user(){ return $this->belongsTo(User::class); }

  public function subCompras(){ return $this->hasMany(SubCompra::class); }
}
