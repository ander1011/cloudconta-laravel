<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

class EmpresaPolicy
{
    /**
     * Determine if the given empresa can be viewed by the user.
     */
    public function view(User $user, Empresa $empresa): bool
    {
        return $user->id === $empresa->usuario_id;
    }

    /**
     * Determine if the given empresa can be updated by the user.
     */
    public function update(User $user, Empresa $empresa): bool
    {
        return $user->id === $empresa->usuario_id;
    }

    /**
     * Determine if the given empresa can be deleted by the user.
     */
    public function delete(User $user, Empresa $empresa): bool
    {
        return $user->id === $empresa->usuario_id;
    }
}
