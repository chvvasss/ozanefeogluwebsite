<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

/**
 * Wraps Fortify's 2FA actions in admin-scoped screens.
 */
class TwoFactorController extends Controller
{
    public function setup(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('admin.profile.two-factor', [
            'user' => $user,
            'hasPendingSetup' => ! empty($user->two_factor_secret) && empty($user->two_factor_confirmed_at),
            'enabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    public function enable(Request $request, EnableTwoFactorAuthentication $enable): RedirectResponse
    {
        $enable($request->user(), force: false);

        return back()->with('status', __('QR kodu oluşturuldu. Uygulamanızdan tarayın ve altta doğrulayın.'));
    }

    public function confirm(
        Request $request,
        ConfirmTwoFactorAuthentication $confirm
    ): RedirectResponse {
        $confirm($request->user(), (string) $request->input('code', ''));

        activity('auth')
            ->causedBy($request->user())
            ->event('2fa.enabled')
            ->log('2fa.enabled');

        return redirect()
            ->route('admin.two-factor.setup')
            ->with('status', __('İki faktörlü doğrulama etkinleştirildi.'));
    }

    public function disable(Request $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        $request->validate(['current_password' => ['required', 'current_password:web']]);
        $disable($request->user());

        activity('auth')
            ->causedBy($request->user())
            ->event('2fa.disabled')
            ->log('2fa.disabled');

        return back()->with('warning', __('İki faktörlü doğrulama kapatıldı. Bir an önce tekrar etkinleştirmenizi öneririz.'));
    }

    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate): RedirectResponse
    {
        $generate($request->user());

        return back()->with('status', __('Yeni kurtarma kodları oluşturuldu. Önceki kodlar geçersiz.'));
    }
}
