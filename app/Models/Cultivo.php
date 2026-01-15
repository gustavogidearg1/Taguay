<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    protected $table = 'cultivos';

    protected $fillable = [
        'name',
        'codfinneg',
        'filtro_power_bi',
    ];

    protected $casts = [
        'filtro_power_bi' => 'boolean',
    ];
}
