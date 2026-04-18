<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Assigns a unique request identifier for cross-log correlation.
 * Propagated via `X-Request-Id` header and log context.
 */
class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $incoming = $request->header('X-Request-Id');
        $id = is_string($incoming) && preg_match('/^[a-zA-Z0-9\-]{8,64}$/', $incoming)
            ? $incoming
            : (string) Str::uuid();

        $request->attributes->set('request_id', $id);

        Log::shareContext(['request_id' => $id]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);

        return $response;
    }
}
