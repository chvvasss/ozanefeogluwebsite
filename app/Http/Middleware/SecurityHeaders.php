<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets HTTP security headers on every response.
 * CSP uses a per-request nonce available in views via Vite helper / blade directive.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = bin2hex(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        $response = $next($request);

        $isProd = app()->environment('production');

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($isProd) {
            $maxAge = (int) config('security.hsts.max_age', 63072000);
            $response->headers->set('Strict-Transport-Security', "max-age={$maxAge}; includeSubDomains; preload");
        }

        if (config('security.csp.enabled') && ! $response->headers->has('Content-Security-Policy')) {
            $csp = $this->buildCsp($nonce);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }

    private function buildCsp(string $nonce): string
    {
        $reportUri = config('security.csp.report_uri');
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'nonce-{$nonce}' 'unsafe-hashes'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
            'upgrade-insecure-requests',
        ];
        if ($reportUri) {
            $directives[] = "report-uri {$reportUri}";
        }

        return implode('; ', $directives);
    }
}
