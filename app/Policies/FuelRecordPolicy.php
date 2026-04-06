<?php

namespace App\Policies;

use App\Models\FuelRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FuelRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Gerente y Supervisor ven todos los registros.
     * Motorista solo ve los suyos.
     */
    public function view(User $user, FuelRecord $fuelRecord): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && $fuelRecord->driver_id === $user->id;
    }

    /**
     * Solo el Motorista puede reportar cargas de combustible.
     */
    public function report(User $user): bool
    {
        return $user->hasPermissionTo('fuel.report');
    }

    /**
     * Solo el Supervisor puede validar registros de combustible.
     */
    public function validate(User $user): bool
    {
        return $user->hasPermissionTo('fuel.validate');
    }

    /**
     * Solo el Motorista puede crear registros de combustible.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('motorista');
    }
}