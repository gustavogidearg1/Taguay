<?php

namespace App\Policies;

use App\Models\Hacienda;
use App\Models\User;

class HaciendaPolicy
{
    public function viewAny(User $user): bool
    {
        // El permiso de módulo ya está en middleware (ver_ganadero)
        // Acá dejamos pasar a cualquiera autenticado.
        return true;
    }

    public function view(User $user, Hacienda $hacienda): bool
    {
        if ($user->hasRole('admin')) return true;

        return (int)$hacienda->user_id === (int)$user->id;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('admin')) return true;

        return true; // si tiene ver_ganadero, puede crear (middleware ya lo filtra)
    }

    public function update(User $user, Hacienda $hacienda): bool
    {
        if ($user->hasRole('admin')) return true;

        return (int)$hacienda->user_id === (int)$user->id;
    }

    public function delete(User $user, Hacienda $hacienda): bool
    {
        if ($user->hasRole('admin')) return true;

        return (int)$hacienda->user_id === (int)$user->id;
    }
}
