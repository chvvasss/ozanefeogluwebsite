<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\Writing;
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
                'writings_total' => Writing::query()->count(),
                'writings_published' => Writing::query()->where('status', 'published')->count(),
                'writings_draft' => Writing::query()->where('status', 'draft')->count(),
                'writings_scheduled' => Writing::query()->where('status', 'scheduled')->count(),
                'photos_total' => class_exists(Photo::class) ? Photo::query()->count() : 0,
                'messages_new' => class_exists(ContactMessage::class)
                    ? ContactMessage::query()->where('status', 'new')->count()
                    : 0,
                'recent_logins' => Activity::query()
                    ->with('causer')
                    ->where('log_name', 'auth')
                    ->where('event', 'login.success')
                    ->latest()
                    ->limit(5)
                    ->get(),
                'recent_published' => Writing::query()
                    ->where('status', 'published')
                    ->orderByDesc('published_at')
                    ->limit(5)
                    ->get(['id', 'title', 'slug', 'kind', 'location', 'published_at', 'read_minutes']),
                'open_drafts' => Writing::query()
                    ->whereIn('status', ['draft', 'scheduled'])
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get(['id', 'title', 'slug', 'status', 'updated_at']),
                'new_messages' => class_exists(ContactMessage::class)
                    ? ContactMessage::query()
                        ->where('status', 'new')
                        ->latest('created_at')
                        ->limit(4)
                        ->get(['id', 'name', 'subject', 'created_at'])
                    : collect(),
            ],
        ]);
    }
}
