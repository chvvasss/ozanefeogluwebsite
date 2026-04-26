<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

/**
 * ContactMessage policy.
 *
 *   super-admin  — full CRUD via before()
 *   admin        — full CRUD
 *   editor       — viewAny + view + update (status changes); cannot delete
 *   contributor  — denied
 *   viewer       — denied
 */
class ContactMessagePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->hasRole('admin');
    }
}
