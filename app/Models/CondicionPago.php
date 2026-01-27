<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionPago extends Model
{
    protected $table = 'condicion_pagos';

    protected $fillable = [
        'name',
        'codigo',
        'div_mes',
        'num_dias',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'div_mes' => 'integer',
        'num_dias' => 'integer',
    ];
}
