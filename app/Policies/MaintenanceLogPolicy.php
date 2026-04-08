<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('supervisor');
    }

    public function approve(User $user, MaintenanceLog $order): bool
    {
        if ($order->cost > 200) {
            return $user->hasRole('gerente');
        }

        return $user->hasRole(['gerente', 'supervisor']);
    }

    public function update(User $user, MaintenanceLog $order): bool
    {
        return $user->hasRole('supervisor') && $order->status === 'pending';
    }
}