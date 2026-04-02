<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentPolicy
{
    use HandlesAuthorization;

    /**
     * Gerente y Supervisor ven todos los incidentes.
     * Motorista solo ve los incidentes que él mismo reportó.
     */
    public function view(User $user, Incident $incident): bool
    {
        if ($user->hasRole(['gerente', 'supervisor'])) {
            return true;
        }

        return $user->hasRole('motorista') && $incident->reported_by === $user->id;
    }

    /**
     * Solo el Motorista puede reportar incidentes con descripción y evidencia.
     */
    public function report(User $user): bool
    {
        return $user->hasPermissionTo('incidents.report');
    }

    /**
     * Solo el Supervisor puede actualizar el estado de un incidente.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('incidents.manage');
    }

    /**
     * Crear un incidente equivale a reportarlo: solo Motorista.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('motorista');
    }
}