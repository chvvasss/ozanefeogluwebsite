<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * User management policy.
 *
 * Role matrix:
 *
 *   super-admin  — full CRUD, including on other super-admins
 *   admin        — CRUD on non-super-admins; cannot edit/delete super-admin accounts
 *   editor       — denied (user management is admin-only surface)
 *   contributor  — denied
 *   viewer       — denied
 *
 * Rationale: User listesi e-posta + role içerir, KVKK kapsamında düşük
 * gerekliliklilerin görmesine gerek yok. admin+ yeterli.
 *
 * Extra guard: nobody can delete themselves through this UI.
 */
class UserPolicy
{
    public function before(User $actor, string $ability): ?bool
    {
        if ($actor->hasRole('super-admin')) {
            // super-admins may do anything — except the self-delete guard,
            // which is enforced inside `delete()`.
            if ($ability === 'delete') {
                return null;
            }

            return true;
        }

        return null;
    }

    public function viewAny(User $actor): bool
    {
        return $actor->hasRole('admin');
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->hasRole('admin');
    }

    public function create(User $actor): bool
    {
        return $actor->hasRole('admin');
    }

    public function update(User $actor, User $target): bool
    {
        if (! $actor->hasRole('admin')) {
            return false;
        }

        // admins cannot edit super-admins.
        return ! $target->hasRole('super-admin');
    }

    public function delete(User $actor, User $target): bool
    {
        // Nobody deletes themselves via the UI.
        if ($actor->id === $target->id) {
            return false;
        }

        if ($actor->hasRole('super-admin')) {
            return true;
        }

        if (! $actor->hasRole('admin')) {
            return false;
        }

        // admins cannot delete super-admins.
        return ! $target->hasRole('super-admin');
    }
}
