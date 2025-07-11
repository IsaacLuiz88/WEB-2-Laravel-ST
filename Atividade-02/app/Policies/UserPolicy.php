<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function before(User $user, string $ability): bool|null{
        if($user->isAdmin()) {
            return true; // Admin users can perform any action
        }
        return null;
    }

    public function viewDebits(User $user): bool
    {
        return $user->isAdminOrLibrarian();
    }

    public function clearDebit(User $authUser, User $targetUser): bool
    {
        if (!$authUser->isAdminOrLibrarian()) {
            return false;
        }

        return true; // Se o bibliotecário/admin pode zerar o próprio débito também
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Customers, Librarians, and Admins can view the list.
        //return $user->isAdminOrLibrarian();
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Customers, Librarians, and Admins can view a specific item.
        //return $user->isAdminOrLibrarian();
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdminOrLibrarian();
    }

    public function updateRole(User $user, User $model): bool
    {
        // Only Admins can update roles.
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || ($user->isLibrarian() && !$model->isAdmin()) || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
