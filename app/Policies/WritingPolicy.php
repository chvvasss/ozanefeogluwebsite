<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Writing;

/**
 * Role matrix (see docs/discovery/information-architecture.md §6):
 *
 *   super-admin  — everything
 *   admin        — everything except user management (enforced elsewhere)
 *   editor       — full content CRUD + publish
 *   contributor  — create + edit own drafts; cannot publish
 *   viewer       — read-only list/view
 */
class WritingPolicy
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
        return $user->hasAnyRole(['admin', 'editor', 'contributor', 'viewer']);
    }

    public function view(User $user, Writing $writing): bool
    {
        if ($user->hasAnyRole(['admin', 'editor', 'viewer'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $writing->author_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor', 'contributor']);
    }

    public function update(User $user, Writing $writing): bool
    {
        if ($user->hasAnyRole(['admin', 'editor'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $writing->author_id === $user->id
                && $writing->status === 'draft';
        }

        return false;
    }

    public function delete(User $user, Writing $writing): bool
    {
        if ($user->hasAnyRole(['admin', 'editor'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $writing->author_id === $user->id
                && $writing->status === 'draft';
        }

        return false;
    }

    public function publish(User $user, Writing $writing): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function unpublish(User $user, Writing $writing): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function restore(User $user, Writing $writing): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function forceDelete(User $user, Writing $writing): bool
    {
        return false; // only super-admin via before()
    }
}
