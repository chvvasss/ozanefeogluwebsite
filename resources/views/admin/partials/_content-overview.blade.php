@php
    /**
     * Content overview widget.
     *
     * Self-contained: runs its own queries so DashboardController stays untouched.
     * All filters use indexed columns (status, is_published, published_at, created_at).
     *
     * Include via: @include('admin.partials._content-overview')
     */
    use App\Models\Photo;
    use App\Models\Publication;
    use App\Models\Writing;
    use Spatie\Activitylog\Models\Activity;

    $since30 = now()->subDays(30);

    $writingsPublished    = Writing::query()->where('status', 'published')->count();
    $writingsDraft        = Writing::query()->where('status', 'draft')->count();
    $photosPublished      = Photo::query()->where('is_published', true)->count();
    $photosDraft          = Photo::query()->where('is_published', false)->count();
    $publicationsCount    = Publication::query()->count();

    $writingsLast30 = Writing::query()
        ->where('status', 'published')
        ->where('published_at', '>=', $since30)
        ->count();

    $photosLast30 = Photo::query()
        ->where('is_published', true)
        ->where('created_at', '>=', $since30)
        ->count();

    $recentActivity = Activity::query()
        ->with('causer')
        ->whereIn('log_name', ['default', 'content'])
        ->latest()
        ->limit(5)
        ->get();

    $hasAnyWriting = ($writingsPublished + $writingsDraft) > 0;
@endphp

<section class="admin-card mt-6" aria-labelledby="content-overview-title">
    <div class="flex items-baseline justify-between mb-4">
        <p id="content-overview-title" class="admin-card-title mb-0">İçerik özeti</p>
        <span class="text-xs uppercase tracking-[0.15em] text-[var(--color-ink-subtle)]">Son 30 gün</span>
    </div>

    @if (! $hasAnyWriting)
        <div class="text-center py-8">
            <p class="display-fraunces text-xl mb-2">Arşiv boş.</p>
            <p class="text-sm text-[var(--color-ink-muted)]">
                İlk yazını oluşturmak için "Yazılar" sekmesine git.
            </p>
        </div>
    @else
        {{-- Yayın hızı (publishing velocity) --}}
        <div class="grid grid-cols-2 gap-3 mb-6 text-center">
            <div>
                <div class="stat-value text-2xl">{{ $writingsLast30 }}</div>
                <div class="stat-label">yazı · son 30 gün</div>
            </div>
            <div>
                <div class="stat-value text-2xl">{{ $photosLast30 }}</div>
                <div class="stat-label">fotoğraf · son 30 gün</div>
            </div>
        </div>

        {{-- Totals --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6 text-center border-t border-[var(--color-rule)] pt-4">
            <div>
                <div class="stat-value text-xl">{{ $writingsPublished }}</div>
                <div class="stat-label">yayında yazı</div>
            </div>
            <div>
                <div class="stat-value text-xl">{{ $writingsDraft }}</div>
                <div class="stat-label">taslak yazı</div>
            </div>
            <div>
                <div class="stat-value text-xl">{{ $photosPublished }}</div>
                <div class="stat-label">yayında foto</div>
            </div>
            <div>
                <div class="stat-value text-xl">{{ $photosDraft }}</div>
                <div class="stat-label">taslak foto</div>
            </div>
            <div>
                <div class="stat-value text-xl">{{ $publicationsCount }}</div>
                <div class="stat-label">yayın</div>
            </div>
        </div>

        {{-- Recent activity --}}
        <div>
            <p class="text-xs uppercase tracking-[0.15em] text-[var(--color-ink-subtle)] mb-2">
                Son aktiviteler
            </p>
            @if ($recentActivity->isEmpty())
                <p class="text-sm text-[var(--color-ink-muted)]">Henüz içerik aktivitesi yok.</p>
            @else
                <ol class="divide-y divide-[var(--color-rule)] -mx-2">
                    @foreach ($recentActivity as $log)
                        <li class="flex items-baseline justify-between px-2 py-2 text-sm gap-3">
                            <span class="font-mono text-xs text-[var(--color-ink-subtle)] w-32 shrink-0">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                            <span class="flex-1 truncate">
                                <strong>{{ $log->causer?->name ?? 'sistem' }}</strong>
                                {{ $log->event ?? $log->description }}
                                @if ($log->subject_type)
                                    <span class="font-mono text-xs text-[var(--color-ink-subtle)]">
                                        · {{ class_basename($log->subject_type) }}#{{ $log->subject_id }}
                                    </span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    @endif
</section>
