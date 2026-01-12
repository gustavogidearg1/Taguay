<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $table = 'establecimientos';
    protected $fillable = ['nombre', 'ubicacion'];

    public function haciendas()
    {
        return $this->hasMany(Hacienda::class);
    }
}
