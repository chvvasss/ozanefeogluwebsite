@extends('layouts.admin', ['title' => 'Masa'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">{{ now()->locale('tr')->translatedFormat('l, d F Y') }}</p>
        <h1 class="admin-page-title">İyi geldin, <em class="italic text-[var(--color-accent)]">{{ auth()->user()->display_name }}</em>.</h1>
        <p class="admin-page-subtitle">Masa sakin. Arşiv hazır, son denetim temiz.</p>
    </div>
    <div class="flex gap-2">
        <a href="#" class="btn btn--ghost btn--sm" aria-disabled="true">+ Not <small class="opacity-50">Faz 2</small></a>
        <a href="#" class="btn btn--sm" aria-disabled="true">+ Yeni yazı <small class="opacity-50">Faz 2</small></a>
    </div>
</header>

<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="admin-card">
        <p class="admin-card-title">Kullanıcılar</p>
        <div class="stat">
            <div class="stat-value">{{ $metrics['users'] }}</div>
            <div class="stat-label">toplam hesap</div>
        </div>
    </div>
    <div class="admin-card">
        <p class="admin-card-title">Aktif oturum</p>
        <div class="stat">
            <div class="stat-value">{{ $metrics['sessions_open'] }}</div>
            <div class="stat-label">kayıtlı cihaz</div>
        </div>
    </div>
    <div class="admin-card">
        <p class="admin-card-title">Son 30 gün</p>
        <div class="stat">
            <div class="stat-value">—</div>
            <div class="stat-label">ziyaret (Faz 3)</div>
        </div>
    </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-4">
    <div class="admin-card">
        <p class="admin-card-title">Son girişler</p>
        @if (count($metrics['recent_logins']) === 0)
            <p class="text-sm text-[var(--color-ink-muted)]">Bu kadar. İşlem geçmişin hep temiz kalsın.</p>
        @else
            <ol class="divide-y divide-[var(--color-rule)] -mx-2">
                @foreach ($metrics['recent_logins'] as $log)
                    <li class="flex items-baseline justify-between px-2 py-3 text-sm">
                        <span class="font-mono text-xs text-[var(--color-ink-subtle)] w-32 shrink-0">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </span>
                        <span class="flex-1">{{ $log->causer?->name ?? 'Bilinmeyen' }}</span>
                        <span class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)]">
                            {{ $log->properties['ip'] ?? '—' }}
                        </span>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    <aside class="admin-card">
        <p class="admin-card-title">Sıradaki fazlar</p>
        <ul class="text-sm space-y-3 text-[var(--color-ink-muted)]">
            <li class="flex items-baseline gap-3">
                <span class="font-mono text-xs text-[var(--color-accent)]">Faz 2</span>
                İçerik çekirdeği — projeler, yazılar, kategoriler, medya.
            </li>
            <li class="flex items-baseline gap-3">
                <span class="font-mono text-xs text-[var(--color-ink-subtle)]">Faz 3</span>
                Public'e cila — anasayfa detayları, SEO, RSS, arama.
            </li>
            <li class="flex items-baseline gap-3">
                <span class="font-mono text-xs text-[var(--color-ink-subtle)]">Faz 4</span>
                Gelişmiş admin — menü, tema, yedekleme, kullanıcı yönetimi.
            </li>
        </ul>
    </aside>
</section>

@include('admin.partials._content-overview')

@role('super-admin|admin')
    @include('admin.partials._security-overview')
@endrole
@endsection
