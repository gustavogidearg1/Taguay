<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Lluvia extends Model
{
use HasFactory;


protected $fillable = [
'company_id','establecimiento_id','fecha','hora','mm','fuente',
'observador','comentario','archivo_path','estacion_nombre','lat','lng','user_id'
];


protected $casts = [
    'fecha' => 'date',
    'hora'  => 'datetime', // <-- sin formato acá
    'mm'    => 'decimal:1',
    'lat'   => 'decimal:7',
    'lng'   => 'decimal:7',
];


// Relaciones
public function establecimiento()
{
return $this->belongsTo(Establecimiento::class);
}


public function user()
{
return $this->belongsTo(User::class);
}


public function company()
{
return $this->belongsTo(Company::class);
}
}
