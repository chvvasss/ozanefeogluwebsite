<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * Backup ability policy.
 *
 * Backups are file-based (no Eloquent model), so we bind this as a named
 * ability via Gate::define('manage-backups', [BackupPolicy::class, 'manage'])
 * rather than Gate::policy(Model::class, ...).
 *
 * Role matrix:
 *   super-admin — full access (before() short-circuit)
 *   admin       — full access
 *   others      — denied
 */
class BackupPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function manage(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function viewAny(User $user): bool
    {
        return $this->manage($user);
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function delete(User $user): bool
    {
        return $this->manage($user);
    }

    public function download(User $user): bool
    {
        return $this->manage($user);
    }
}
