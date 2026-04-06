<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceLogPolicy
{
    use HandlesAuthorization;

    /**
     * Gerente y Supervisor pueden ver el historial de mantenimientos.
     * Motorista no tiene acceso.
     */
    public function view(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    /**
     * Solo el Supervisor puede programar/crear órdenes de mantenimiento.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('supervisor');
    }

    /**
     * Lógica clave del negocio:
     * — Órdenes con costo <= $200: Supervisor puede aprobarlas.
     * — Órdenes con costo >  $200: solo el Gerente puede aprobarlas.
     */
    public function approve(User $user, MaintenanceLog $order): bool
    {
        if ($order->estimated_cost > 200) {
            return $user->hasRole('gerente');
        }

        return $user->hasRole(['gerente', 'supervisor']);
    }

    /**
     * El Supervisor puede editar órdenes que aún no fueron aprobadas.
     */
    public function update(User $user, MaintenanceLog $order): bool
    {
        return $user->hasRole('supervisor') && $order->status === 'pending';
    }
}