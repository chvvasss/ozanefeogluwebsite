@php
    /**
     * Security overview widget.
     *
     * Reads directly from the activity log (no controller changes needed).
     * Include via: @include('admin.partials._security-overview')
     */
    use Spatie\Activitylog\Models\Activity;
    use App\Models\UserDevice;

    $since = now()->subDay();

    $failedLogins = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login.failed')
        ->latest()
        ->limit(10)
        ->get();

    $failed24h = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login.failed')
        ->where('created_at', '>=', $since)
        ->count();

    $success24h = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login.success')
        ->where('created_at', '>=', $since)
        ->count();

    $activeSessions = UserDevice::query()->count();
@endphp

<section class="admin-card mt-6" aria-labelledby="security-overview-title">
    <div class="flex items-baseline justify-between mb-4">
        <p id="security-overview-title" class="admin-card-title mb-0">Güvenlik özeti</p>
        <span class="text-xs uppercase tracking-[0.15em] text-[var(--color-ink-subtle)]">Son 24 saat</span>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-6 text-center">
        <div>
            <div class="stat-value text-2xl">{{ $success24h }}</div>
            <div class="stat-label">başarılı giriş</div>
        </div>
        <div>
            <div class="stat-value text-2xl {{ $failed24h > 10 ? 'text-red-500' : '' }}">
                {{ $failed24h }}
            </div>
            <div class="stat-label">başarısız deneme</div>
        </div>
        <div>
            <div class="stat-value text-2xl">{{ $activeSessions }}</div>
            <div class="stat-label">aktif oturum</div>
        </div>
    </div>

    <div>
        <p class="text-xs uppercase tracking-[0.15em] text-[var(--color-ink-subtle)] mb-2">
            Son başarısız denemeler
        </p>
        @if ($failedLogins->isEmpty())
            <p class="text-sm text-[var(--color-ink-muted)]">Son başarısız deneme yok. Temiz.</p>
        @else
            <ol class="divide-y divide-[var(--color-rule)] -mx-2">
                @foreach ($failedLogins as $log)
                    <li class="flex items-baseline justify-between px-2 py-2 text-sm">
                        <span class="font-mono text-xs text-[var(--color-ink-subtle)] w-32 shrink-0">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </span>
                        <span class="flex-1 truncate">
                            {{ $log->properties['email_attempted'] ?? '—' }}
                        </span>
                        <span class="font-mono text-xs text-[var(--color-ink-subtle)]">
                            {{ $log->properties['ip'] ?? '—' }}
                        </span>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>
