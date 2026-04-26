<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

/**
 * Role matrix (see docs/discovery/information-architecture.md §6):
 *
 *   super-admin  — everything (via before())
 *   admin        — full CRUD
 *   editor       — full CRUD
 *   contributor  — viewAny only (needs list for writing editor)
 *   viewer       — viewAny only
 */
class PublicationPolicy
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

    public function view(User $user, Publication $publication): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function update(User $user, Publication $publication): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }
}
