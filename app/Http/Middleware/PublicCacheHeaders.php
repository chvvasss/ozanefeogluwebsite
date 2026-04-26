<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets a short shared-cache lifetime on public GET responses.
 *
 * 5 minutes (`public, max-age=300, s-maxage=300`) — long enough to absorb
 * burst traffic via reverse-proxy/CDN, short enough that admin edits to
 * settings/content propagate quickly. Authenticated requests, error pages,
 * and non-GET methods are skipped automatically.
 */
class PublicCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache successful, idempotent, anonymous GETs.
        if ($request->method() !== 'GET') {
            return $response;
        }
        if ($response->getStatusCode() !== 200) {
            return $response;
        }
        if ($request->user() !== null) {
            return $response;
        }

        // Skip routes that already declare their own cache policy.
        if ($response->headers->has('Cache-Control')) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=300');
        $response->headers->set('Vary', 'Accept-Encoding, Cookie');

        return $response;
    }
}
