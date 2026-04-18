<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When 2FA is mandatory for admins, redirect them to the enrollment flow
 * unless they are already on a 2FA route.
 */
class EnsureTwoFactorEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->requiresTwoFactor()) {
            return $next($request);
        }

        if ($user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        $allowList = [
            'admin.two-factor.setup',
            'admin.two-factor.enable',
            'admin.two-factor.confirm',
            'logout',
        ];
        if (in_array((string) $request->route()?->getName(), $allowList, true)) {
            return $next($request);
        }

        return redirect()
            ->route('admin.two-factor.setup')
            ->with('warning', __('İki faktörlü doğrulamayı kurulduktan sonra devam edebilirsiniz.'));
    }
}
