<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function view(User $user, Incident $incident): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && (int) $incident->user_id === (int) $user->id;
    }

    public function report(User $user): bool
    {
        return $user->hasRole('motorista');
    }

    public function manage(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('motorista');
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }
}