<?php

namespace App\Policies;

use App\Models\MaintenanceOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Gerente y Supervisor pueden ver el historial de mantenimientos.
     * Motorista no tiene acceso.
     */
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('maintenance.view');
    }

    /**
     * Solo el Supervisor puede programar/crear órdenes de mantenimiento.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('maintenance.schedule');
    }

    /**
     * Lógica clave del negocio:
     * — Órdenes con costo <= $200: Supervisor puede aprobarlas.
     * — Órdenes con costo >  $200: solo el Gerente puede aprobarlas.
     */
    public function approve(User $user, MaintenanceOrder $order): bool
    {
        if ($order->estimated_cost > 200) {
            return $user->hasPermissionTo('maintenance.approve'); // solo Gerente
        }

        return $user->hasRole(['gerente', 'supervisor']);
    }

    /**
     * El Supervisor puede editar órdenes que aún no fueron aprobadas.
     */
    public function update(User $user, MaintenanceOrder $order): bool
    {
        return $user->hasRole('supervisor') && $order->status === 'pending';
    }
}