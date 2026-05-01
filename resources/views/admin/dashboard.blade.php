@extends('layouts.admin', ['title' => 'Masa'])

@section('content')

{{-- ─────────── PAGE HEADER ─────────── --}}
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">{{ now()->locale('tr')->translatedFormat('l, d F Y') }}</p>
        <h1 class="admin-page-title">İyi geldin, <em class="italic text-[var(--color-accent)]">{{ auth()->user()->display_name ?? auth()->user()->name }}</em>.</h1>
        <p class="admin-page-subtitle">Masa sakin. Arşiv hazır, son denetim temiz.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.writings.index') }}" class="btn btn--secondary btn--sm">Yazılar</a>
        <a href="{{ route('admin.writings.create') }}" class="btn btn--sm">+ Yeni yazı</a>
    </div>
</header>

{{-- ─────────── KPI TILES ─────────── --}}
<section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
    <div class="kpi-tile">
        <p class="kpi-eyebrow">Yayında</p>
        <div class="kpi-value">{{ $metrics['writings_published'] }}</div>
        <p class="kpi-foot">yazı</p>
    </div>
    <div class="kpi-tile">
        <p class="kpi-eyebrow">Açık</p>
        <div class="kpi-value">{{ $metrics['writings_draft'] + $metrics['writings_scheduled'] }}</div>
        <p class="kpi-foot">{{ $metrics['writings_draft'] }} taslak · {{ $metrics['writings_scheduled'] }} zamanlanmış</p>
    </div>
    <div class="kpi-tile">
        <p class="kpi-eyebrow">Görüntü</p>
        <div class="kpi-value">{{ $metrics['photos_total'] }}</div>
        <p class="kpi-foot">arşiv fotoğrafı</p>
    </div>
    <div class="kpi-tile {{ $metrics['messages_new'] > 0 ? 'is-accent' : '' }}">
        <p class="kpi-eyebrow">Gelen kutusu</p>
        <div class="kpi-value">{{ $metrics['messages_new'] }}</div>
        <p class="kpi-foot">okunmamış mesaj</p>
    </div>
</section>

{{-- ─────────── DOSSIER GRID — recent published + sidebar ─────────── --}}
<section class="dossier-grid mb-10">

    {{-- Left: Son yayımlananlar table --}}
    <div class="dg-7">
        <header class="flex items-baseline justify-between mb-4">
            <h2 class="display-quiet">Son yayımlananlar</h2>
            <a href="{{ route('admin.writings.index') }}" class="link-quiet text-sm">Tümü →</a>
        </header>

        @if ($metrics['recent_published']->isEmpty())
            <div class="admin-card text-sm text-[var(--color-ink-muted)] italic">
                Henüz yayında yazı yok. <a href="{{ route('admin.writings.create') }}" class="underline">İlk yazıyı yaz</a>.
            </div>
        @else
            <div class="admin-table-wrap" style="border:1px solid var(--color-rule); background: var(--color-bg);">
                <table class="admin-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:10px 14px; border-bottom:1px solid var(--color-rule); font-family:var(--font-mono); font-size:.62rem; letter-spacing:.18em; text-transform:uppercase; color:var(--color-ink-subtle); font-weight:500;">Başlık</th>
                            <th style="text-align:left; padding:10px 14px; border-bottom:1px solid var(--color-rule); font-family:var(--font-mono); font-size:.62rem; letter-spacing:.18em; text-transform:uppercase; color:var(--color-ink-subtle); font-weight:500;">Tür</th>
                            <th style="text-align:right; padding:10px 14px; border-bottom:1px solid var(--color-rule); font-family:var(--font-mono); font-size:.62rem; letter-spacing:.18em; text-transform:uppercase; color:var(--color-ink-subtle); font-weight:500;">Tarih</th>
                            <th style="text-align:right; padding:10px 14px; border-bottom:1px solid var(--color-rule); font-family:var(--font-mono); font-size:.62rem; letter-spacing:.18em; text-transform:uppercase; color:var(--color-ink-subtle); font-weight:500;">Süre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($metrics['recent_published'] as $w)
                            <tr style="border-bottom:1px solid var(--color-rule);">
                                <td style="padding:14px;">
                                    <div class="col-title">
                                        <a href="{{ route('admin.writings.edit', $w) }}">{{ $w->title }}</a>
                                    </div>
                                    @if ($w->location)
                                        <div class="col-meta">{{ $w->location }}</div>
                                    @endif
                                </td>
                                <td style="padding:14px;" class="col-num">{{ ucfirst($w->kind ?? '—') }}</td>
                                <td style="padding:14px; text-align:right;" class="col-num">
                                    {{ $w->published_at?->format('d.m.Y') ?? '—' }}
                                </td>
                                <td style="padding:14px; text-align:right;" class="col-num">
                                    {{ $w->read_minutes ? $w->read_minutes.' dk' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Right: Açık taslaklar + Yeni mesajlar --}}
    <aside class="dg-5">
        <header class="flex items-baseline justify-between mb-4">
            <h2 class="display-quiet">Açık taslaklar</h2>
            <a href="{{ route('admin.writings.index') }}?status=draft" class="link-quiet text-sm">→</a>
        </header>

        @if ($metrics['open_drafts']->isEmpty())
            <div class="admin-card text-sm text-[var(--color-ink-muted)] italic">
                Açık taslak yok.
            </div>
        @else
            <ul class="soft-list">
                @foreach ($metrics['open_drafts'] as $d)
                    <li class="soft-list-item">
                        <div style="min-width: 0; flex: 1;">
                            <div class="soft-list-name">
                                <a href="{{ route('admin.writings.edit', $d) }}" class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)]">
                                    {{ $d->title }}
                                </a>
                            </div>
                            <div class="soft-list-meta">son düzenleme {{ $d->updated_at?->diffForHumans() }}</div>
                        </div>
                        <span class="status-pip" data-status="{{ $d->status }}">
                            {{ $d->status === 'draft' ? 'Taslak' : 'Zamanlandı' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif

        <header class="flex items-baseline justify-between mt-6 mb-4">
            <h2 class="display-quiet">Yeni mesajlar</h2>
            <a href="{{ route('admin.contact.index') }}" class="link-quiet text-sm">Gelen kutusu →</a>
        </header>

        @if ($metrics['new_messages']->isEmpty())
            <div class="admin-card text-sm text-[var(--color-ink-muted)] italic">
                Gelen kutusu temiz.
            </div>
        @else
            <ul class="soft-list">
                @foreach ($metrics['new_messages'] as $m)
                    <li class="soft-list-item" style="flex-direction: column; align-items: stretch; gap: 4px;">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="soft-list-name">{{ $m->name }}</span>
                            <span class="soft-list-meta">{{ $m->created_at?->format('d.m H:i') ?? '—' }}</span>
                        </div>
                        @if ($m->subject)
                            <div class="soft-list-subj">{{ $m->subject }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </aside>
</section>

{{-- ─────────── EXISTING PARTIALS (preserved) ─────────── --}}
@includeIf('admin.partials._content-overview')

@role('super-admin|admin')
    @includeIf('admin.partials._security-overview')
@endrole

@endsection
