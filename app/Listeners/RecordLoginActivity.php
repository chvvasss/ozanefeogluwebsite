<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\Auth\LoginActivityRecorder;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;

class RecordLoginActivity
{
    public function __construct(private readonly LoginActivityRecorder $recorder) {}

    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }
        $this->recorder->recordSuccess($user, request());
    }

    public function handleFailed(Failed $event): void
    {
        $email = (string) ($event->credentials['email'] ?? '');
        $this->recorder->recordFailure($email !== '' ? $email : null, request(), 'bad_credentials');
    }
}
