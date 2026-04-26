<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;

/**
 * PhotoPolicy — mirrors WritingPolicy role matrix.
 *
 * Contributors can create + edit their OWN unpublished photos; they may
 * not publish, cannot edit someone else's photo, and cannot touch
 * already-published items. Editors and admins have full CRUD + publish.
 */
class PhotoPolicy
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

    public function view(User $user, Photo $photo): bool
    {
        if ($user->hasAnyRole(['admin', 'editor', 'viewer'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $photo->created_by === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor', 'contributor']);
    }

    public function update(User $user, Photo $photo): bool
    {
        if ($user->hasAnyRole(['admin', 'editor'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $photo->created_by === $user->id
                && ! $photo->is_published;
        }

        return false;
    }

    public function delete(User $user, Photo $photo): bool
    {
        if ($user->hasAnyRole(['admin', 'editor'])) {
            return true;
        }

        if ($user->hasRole('contributor')) {
            return $photo->created_by === $user->id
                && ! $photo->is_published;
        }

        return false;
    }

    public function restore(User $user, Photo $photo): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function forceDelete(User $user, Photo $photo): bool
    {
        return false; // super-admin only via before()
    }
}
