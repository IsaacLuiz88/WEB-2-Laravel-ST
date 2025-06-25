<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PublisherPolicy
{
    public function before(?User $user, string $ability): bool|null
    {
        // Admin users can perform any action.
        if ($user && $user->isAdmin()) {
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Customers, Librarians, and Admins can view the list.
        //return $user->isClient() || $user->isAdminOrLibrarian();
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Publisher $publisher): bool
    {
        // Customers, Librarians, and Admins can view a specific item.
        //return $user->isClient() || $user->isAdminOrLibrarian();
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdminOrLibrarian();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Publisher $publisher): bool
    {
        return $user->isAdminOrLibrarian();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Publisher $publisher): bool
    {
        return $user->isAdminOrLibrarian();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Publisher $publisher): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Publisher $publisher): bool
    {
        return false;
    }
}
