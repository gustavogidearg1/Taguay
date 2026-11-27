<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'telefono_whatsapp',   // solo dígitos con código de país, ej.: 5493534191741
        'password',
    ];

    /**
     * Normaliza un teléfono al formato con el que WhatsApp identifica a la persona:
     * solo dígitos y código de país. Para Argentina agrega el 9 de celular si falta.
     *   "+54 9 3534 19-1741" → 5493534191741
     *   "+54 3534 191741"    → 5493534191741
     *   "3534 191741"        → 5493534191741  (10 dígitos: se asume celular argentino)
     * Devuelve null si queda vacío.
     */
    public static function normalizarTelefono(?string $telefono): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $telefono);

        if ($digitos === '') {
            return null;
        }
        if (strlen($digitos) === 10) {
            return '549' . $digitos;
        }
        if (str_starts_with($digitos, '54') && ! str_starts_with($digitos, '549') && strlen($digitos) === 12) {
            return '549' . substr($digitos, 2);
        }

        return $digitos;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
