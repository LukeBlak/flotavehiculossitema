<?php

namespace App\Policies;

use App\Models\FuelLog;
use App\Models\User;

class FuelLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function view(User $user, FuelLog $fuelLog): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && (int) $fuelLog->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('motorista');
    }

    public function update(User $user, FuelLog $fuelLog): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function delete(User $user, FuelLog $fuelLog): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function restore(User $user, FuelLog $fuelLog): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function forceDelete(User $user, FuelLog $fuelLog): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }
}
