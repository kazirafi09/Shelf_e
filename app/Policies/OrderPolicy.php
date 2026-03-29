<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders (Admin Order List).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view a specific order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admins can see any order. Normal users can ONLY see their own.
        return $user->isAdmin() || $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can update the order (e.g., change status).
     */
    public function update(User $user, Order $order): bool
    {
        // Only admins are allowed to change an order's status
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admins can delete orders (if you ever build a delete feature)
        return $user->isAdmin();
    }
}