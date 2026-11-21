<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCultivo extends Model
{
    protected $table = 'estados_cultivo';

    protected $fillable = [
        'nombre',
        'activo',
        'por_defecto',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'por_defecto' => 'boolean',
    ];

    public function loteEstados()
    {
        return $this->hasMany(LoteEstado::class, 'estado_cultivo_id');
    }

    /**
     * Devuelve el estado activo marcado como "por defecto" (o null si no hay ninguno).
     */
    public static function porDefecto(): ?self
    {
        return static::where('activo', true)
            ->where('por_defecto', true)
            ->first();
    }

    /**
     * Desmarca "por defecto" en todos los demás estados, para que quede uno solo.
     */
    public function marcarComoUnicoPorDefecto(): void
    {
        static::where('id', '!=', $this->id)
            ->where('por_defecto', true)
            ->update(['por_defecto' => false]);
    }
}
