<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Contracts\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'metrics' => [
                'users' => User::query()->count(),
                'sessions_open' => UserDevice::query()->count(),
                'recent_logins' => Activity::query()
                    ->with('causer')
                    ->where('log_name', 'auth')
                    ->where('event', 'login.success')
                    ->latest()
                    ->limit(5)
                    ->get(),
            ],
        ]);
    }
}
