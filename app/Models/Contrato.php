<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $fillable = [
        'nro_contrato','num_forward','fecha',
        'entrega_inicial','entrega_final',
        'campania_id','cultivo_id','moneda_id',
        'caracteristica_precio','formacion_precio','condicion_precio','condicion_pago','lista_grano',
        'cliente_codigo','cliente_nombre','vendedor',
        'destino','formato','disponible_tipo',
        'definicion',
        'cantidad_tn','precio','precio_fijado',
        'comision','paritaria','volatil','obs','importante',
        'created_by','updated_by','organizacion_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'entrega_inicial' => 'date',
        'entrega_final' => 'date',
        'cantidad_tn' => 'decimal:2',
        'precio' => 'decimal:2',
        'precio_fijado' => 'decimal:2',
    ];

    public function campania() { return $this->belongsTo(Campania::class); }
    public function cultivo()  { return $this->belongsTo(Cultivo::class); }
    public function moneda()   { return $this->belongsTo(Moneda::class); }

    public function subContratos()
{
  return $this->hasMany(\App\Models\SubContrato::class)->orderBy('fecha');
}

public function organizacion()
{
  return $this->belongsTo(\App\Models\Organizacion::class, 'organizacion_id');
}
}
