<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function logout(User $user): bool
    {
        return !$user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return !$user->is_admin;
    }

    public function getOrders(User $user): bool
    {
        return !$user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return !$user->is_admin;
    }
}
