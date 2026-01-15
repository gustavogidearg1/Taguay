<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campania extends Model
{
    protected $table = 'campanias';

    protected $fillable = [
        'name',
        'codfinneg',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
