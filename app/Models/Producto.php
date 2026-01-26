<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'name',
        'codigo',
        'unidad_id',
        'tipo_producto_id',
        'activo',
        'stock',
        'vende',
        'minimo',
        'maximo',
        'obser',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'stock'  => 'boolean',
        'vende'  => 'boolean',
        'minimo' => 'decimal:2',
        'maximo' => 'decimal:2',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function tipoProducto()
    {
        return $this->belongsTo(TipoProducto::class);
    }
}
