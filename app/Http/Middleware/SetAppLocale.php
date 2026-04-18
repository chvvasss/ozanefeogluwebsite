<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = explode(',', (string) env('AVAILABLE_LOCALES', 'tr,en'));
        $available = array_values(array_filter(array_map('trim', $available)));
        $fallback = (string) config('app.fallback_locale', 'tr');

        $locale = $request->route('locale');
        if (! is_string($locale) || ! in_array($locale, $available, true)) {
            $user = $request->user();
            $locale = ($user && in_array((string) $user->locale, $available, true))
                ? (string) $user->locale
                : $fallback;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
