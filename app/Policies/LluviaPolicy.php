<?php

namespace App\Policies;

use App\Models\Lluvia;
use App\Models\User;

class LluviaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver_agricola');
    }

    public function view(User $user, Lluvia $lluvia): bool
    {
        if ($user->hasRole('admin')) return true;
        return $lluvia->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('ver_agricola');
    }

    public function update(User $user, Lluvia $lluvia): bool
    {
        if ($user->hasRole('admin')) return true;
        return $lluvia->user_id === $user->id;
    }

    public function delete(User $user, Lluvia $lluvia): bool
    {
        if ($user->hasRole('admin')) return true;
        return $lluvia->user_id === $user->id;
    }
}
