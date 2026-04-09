<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor', 'motorista']);
    }

    /**
     * Gerente y Supervisor pueden ver todos los viajes.
     * Motorista solo ve los viajes asignados a su cuenta.
     */
    public function view(User $user, Trip $trip): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && $trip->driver_id === $user->id;
    }

    /**
     * Solo el Supervisor puede asignar viajes.
     */
    public function assign(User $user): bool
    {
        return $user->hasPermissionTo('trips.assign');
    }

    /**
     * Solo el Motorista asignado puede registrar km inicial/final.
     */
    public function registerKm(User $user, Trip $trip): bool
    {
        return $user->hasRole('motorista')
            && (int) $trip->driver_id === (int) $user->id;
    }

    /**
     * Solo el Supervisor puede validar km y rutas reportadas.
     */
    public function validate(User $user): bool
    {
        return $user->hasPermissionTo('trips.validate');
    }

    /**
     * Solo el Supervisor puede crear/asignar viajes.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('supervisor');
    }

    public function update(User $user, Trip $trip): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && (int) $trip->driver_id === (int) $user->id;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }
}