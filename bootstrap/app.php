<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTwoFactorEnrolled;
use App\Http\Middleware\PublicCacheHeaders;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetAppLocale;
use App\Http\Middleware\ThrottleForgotPassword;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust upstream proxy / CDN headers so $request->ip() returns the real
        // client IP behind Cloudflare, Nginx, or similar reverse proxies.
        // The IP feeds rate limiters and security audit logs — getting it wrong
        // means lockouts protect the proxy instead of the user.
        $middleware->trustProxies(at: '*', headers:
            Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->append(RequestId::class);
        $middleware->append(SecurityHeaders::class);

        $middleware->web(append: [
            SetAppLocale::class,
            PublicCacheHeaders::class,
            ThrottleForgotPassword::class,
        ]);

        $middleware->alias([
            'ensure.2fa' => EnsureTwoFactorEnrolled::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
