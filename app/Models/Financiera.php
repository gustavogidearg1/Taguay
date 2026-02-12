<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financiera extends Model
{
    protected $table = 'financieras';

    protected $fillable = [
        'name',
        'tipo',
        'descripcion',
    ];
}
