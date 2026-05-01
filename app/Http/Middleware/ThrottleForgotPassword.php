<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defends POST /forgot-password from spam / email-harvesting.
 *
 * Limit: 3 requests per hour per (lowercased email + IP).
 * Triggers globally for the password.email route — we cannot easily
 * inject middleware into Fortify's auto-registered routes, so we
 * intercept at the framework boundary.
 */
class ThrottleForgotPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldThrottle($request)) {
            return $next($request);
        }

        $key = $this->key($request);
        $maxAttempts = 3;
        $decaySeconds = 3600;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => __(
                    'Çok fazla şifre sıfırlama isteği. :minutes dakika sonra tekrar dene.',
                    ['minutes' => max(1, (int) ceil($seconds / 60))]
                ),
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }

    private function shouldThrottle(Request $request): bool
    {
        return $request->isMethod('POST')
            && $request->is('forgot-password');
    }

    private function key(Request $request): string
    {
        $email = (string) $request->input('email', '');

        return 'pw-reset|'.Str::transliterate(Str::lower($email)).'|'.(string) $request->ip();
    }
}
