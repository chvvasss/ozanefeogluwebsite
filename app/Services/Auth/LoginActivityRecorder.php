<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginActivityRecorder
{
    public function recordSuccess(User $user, Request $request): void
    {
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'failed_attempts' => 0,
            'locked_until' => null,
        ])->save();

        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ])
            ->event('login.success')
            ->log('login.success');

        UserDevice::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'session_id' => Session::getId(),
            ],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_label' => $this->deriveDeviceLabel((string) $request->userAgent()),
                'last_active_at' => now(),
            ]
        );
    }

    public function recordFailure(?string $email, Request $request, string $reason): void
    {
        activity('auth')
            ->withProperties([
                'email_attempted' => $email,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'reason' => $reason,
            ])
            ->event('login.failed')
            ->log('login.failed');

        if ($email && $user = User::query()->where('email', $email)->first()) {
            $threshold = (int) config('security.login.daily_lockout_threshold', 10);
            $alertAt = (int) config('security.login.alert_threshold', 5);

            $user->increment('failed_attempts');
            $user->refresh();

            // Alert the legitimate owner once we cross the alert threshold —
            // before the account is locked. Avoid spam: send once when crossing
            // the threshold (exact equality), not on every subsequent attempt.
            if ($user->failed_attempts === $alertAt) {
                try {
                    $user->notify(new \App\Notifications\SuspiciousLoginAttempts(
                        attemptCount: $alertAt,
                        accountLocked: false,
                    ));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            if ($user->failed_attempts >= $threshold && $user->locked_until === null) {
                $user->forceFill([
                    'locked_until' => now()->addMinutes((int) config('security.login.lockout_minutes', 15)),
                ])->save();

                try {
                    $user->notify(new \App\Notifications\SuspiciousLoginAttempts(
                        attemptCount: (int) $user->failed_attempts,
                        accountLocked: true,
                    ));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

    public function recordLogout(User $user, Request $request): void
    {
        activity('auth')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->event('logout')
            ->log('logout');

        UserDevice::query()
            ->where('user_id', $user->id)
            ->where('session_id', Session::getId())
            ->delete();
    }

    private function deriveDeviceLabel(string $ua): string
    {
        $ua = strtolower($ua);
        $os = match (true) {
            str_contains($ua, 'windows') => 'Windows',
            str_contains($ua, 'mac os x') || str_contains($ua, 'macintosh') => 'macOS',
            str_contains($ua, 'android') => 'Android',
            str_contains($ua, 'iphone') || str_contains($ua, 'ipad') => 'iOS',
            str_contains($ua, 'linux') => 'Linux',
            default => 'Bilinmeyen',
        };
        $browser = match (true) {
            str_contains($ua, 'edg/') => 'Edge',
            str_contains($ua, 'firefox') => 'Firefox',
            str_contains($ua, 'chrome') && ! str_contains($ua, 'edg/') => 'Chrome',
            str_contains($ua, 'safari') && ! str_contains($ua, 'chrome') => 'Safari',
            default => 'Tarayıcı',
        };

        return "{$browser} · {$os}";
    }
}
