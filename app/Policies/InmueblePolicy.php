<?php

namespace App\Policies;

use App\Models\Inmueble;
use App\Models\User;

class InmueblePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'inmobiliaria']);
    }

    public function view(User $user, Inmueble $inmueble): bool
    {
        return $user->hasRole('admin')
            || (int) $inmueble->inmobiliaria_id === (int) ($user->inmobiliaria->id ?? 0);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'inmobiliaria']);
    }

    public function update(User $user, Inmueble $inmueble): bool
    {
        return $this->view($user, $inmueble);
    }

    public function delete(User $user, Inmueble $inmueble): bool
    {
        return $this->view($user, $inmueble);
    }
}
