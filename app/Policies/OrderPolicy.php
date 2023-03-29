<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->is_admin) {
            return true;
        }
        return $order->user_uuid === $user->uuid;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !$user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return !$user->is_admin && $order->user_uuid === $user->uuid;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->is_admin;
    }


    /**
     * Determine whether the user can download the model as file.
     */
    public function download(User $user, Order $order): bool
    {
        if ($user->is_admin) {
            return true;
        }
        return $order->user_uuid === $user->uuid;
    }


    /**
     * Determine whether the user can view shipment locator.
     */
    public function viewShipmentLocator(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->is_admin;
    }
}
