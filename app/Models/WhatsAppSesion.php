<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Conversación de WhatsApp en curso (máquina de estados de la carga de Avance de Campo).
 * - paso:  en qué pregunta está (establecimiento, lote, cultivo, cantidad, ...).
 * - datos: lo que ya respondió (ids elegidos, kg, ha, patente, ...).
 */
class WhatsAppSesion extends Model
{
    protected $table = 'whatsapp_sesiones';

    protected $fillable = ['telefono', 'user_id', 'paso', 'datos'];

    protected $casts = [
        'datos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Lee un dato guardado de la conversación. */
    public function dato(string $clave, $defecto = null)
    {
        return data_get($this->datos ?? [], $clave, $defecto);
    }

    /** Guarda uno o varios datos (sin perder los anteriores). */
    public function guardar(array $valores, ?string $paso = null): void
    {
        $this->datos = array_merge($this->datos ?? [], $valores);
        if ($paso !== null) {
            $this->paso = $paso;
        }
        $this->save();
    }
}
