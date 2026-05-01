<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

/**
 * Custom authenticator wired via Fortify::authenticateUsing().
 *
 * Enforces:
 *   1. Account lockout (locked_until in the future → reject with countdown)
 *   2. Password hash verification (bcrypt timing-safe via Hash::check)
 *
 * Email verification is enforced separately by Laravel's `verified` middleware
 * which we apply on /admin routes; users with unverified email can log in but
 * will be redirected to /email/verify until they confirm.
 */
class AuthenticateUser
{
    public function __invoke(Request $request): ?User
    {
        $username = (string) $request->input(Fortify::username());

        if ($username === '') {
            return null;
        }

        /** @var User|null $user */
        $user = User::query()
            ->where(Fortify::username(), $username)
            ->first();

        if (! $user) {
            return null;
        }

        // Lockout enforcement — even if password is correct.
        if ($user->isLocked()) {
            $minutesLeft = (int) ceil(now()->diffInSeconds($user->locked_until, true) / 60);

            throw ValidationException::withMessages([
                Fortify::username() => __(
                    'Hesabın :minutes dakika kilitli. Çok sayıda başarısız giriş tespit edildi.',
                    ['minutes' => max(1, $minutesLeft)]
                ),
            ]);
        }

        // Standard password check (timing-safe).
        if (! Hash::check((string) $request->input('password'), $user->password)) {
            return null;
        }

        return $user;
    }
}
