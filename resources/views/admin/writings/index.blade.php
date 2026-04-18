@extends('layouts.admin', ['title' => 'Yazılar'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Yayın · arşiv</p>
        <h1 class="admin-page-title">Yazılar</h1>
        <p class="admin-page-subtitle">{{ $writings->total() }} kayıt</p>
    </div>
    @can('create', App\Models\Writing::class)
        <a href="{{ route('admin.writings.create') }}" class="btn btn--accent">+ Yeni yazı</a>
    @endcan
</header>

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.writings.index') }}"
      class="admin-card flex flex-wrap items-end gap-4 mb-6">
    <div class="field flex-1 min-w-[12rem]">
        <label for="q" class="field-label">Ara</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}"
               placeholder="başlık veya slug…" class="input">
    </div>
    <div class="field w-40">
        <label for="status" class="field-label">Durum</label>
        <select id="status" name="status" class="input">
            <option value="">hepsi</option>
            <option value="draft"     @selected(($filters['status'] ?? '') === 'draft')>taslak</option>
            <option value="scheduled" @selected(($filters['status'] ?? '') === 'scheduled')>planlı</option>
            <option value="published" @selected(($filters['status'] ?? '') === 'published')>yayında</option>
        </select>
    </div>
    <div class="field w-48">
        <label for="kind" class="field-label">Tür</label>
        <select id="kind" name="kind" class="input">
            <option value="">hepsi</option>
            <option value="saha_yazisi" @selected(($filters['kind'] ?? '') === 'saha_yazisi')>saha yazısı</option>
            <option value="roportaj"    @selected(($filters['kind'] ?? '') === 'roportaj')>röportaj</option>
            <option value="deneme"      @selected(($filters['kind'] ?? '') === 'deneme')>deneme</option>
            <option value="not"         @selected(($filters['kind'] ?? '') === 'not')>not</option>
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--sm">Süz</button>
        <a href="{{ route('admin.writings.index') }}" class="btn btn--ghost btn--sm">Temizle</a>
    </div>
</form>

@if ($writings->isEmpty())
    <div class="admin-card text-center py-16">
        <p class="display-fraunces text-2xl mb-2">Henüz yazı yok.</p>
        <p class="text-sm text-[var(--color-ink-muted)] mb-6">
            İlk yazını oluşturmak için sağ üstteki "Yeni yazı" düğmesini kullan.
        </p>
        @can('create', App\Models\Writing::class)
            <a href="{{ route('admin.writings.create') }}" class="btn btn--accent">+ Yeni yazı</a>
        @endcan
    </div>
@else
    <div class="admin-card p-0 overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th class="w-28">Tür</th>
                    <th class="w-28">Durum</th>
                    <th class="w-36">Yayım</th>
                    <th class="w-28">Okuma</th>
                    <th class="text-right w-56">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($writings as $writing)
                    @php
                        $isTrashed = method_exists($writing, 'trashed') && $writing->trashed();
                    @endphp
                    <tr class="{{ $isTrashed ? 'opacity-50' : '' }}">
                        <td>
                            <a href="{{ route('admin.writings.edit', $writing) }}"
                               class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)] font-medium">
                                {{ $writing->getTranslation('title', 'tr', false) ?: '(başlıksız)' }}
                            </a>
                            <div class="text-xs text-[var(--color-ink-subtle)] font-mono mt-1">
                                /{{ $writing->getTranslation('slug', 'tr', false) }}
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center font-mono text-[0.65rem] uppercase tracking-[0.16em] text-[var(--color-ink-muted)]">
                                {{ $writing->kind_label }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'draft'     => ['label' => 'taslak',  'dot' => 'var(--color-ink-subtle)'],
                                    'scheduled' => ['label' => 'planlı',  'dot' => 'var(--color-warning)'],
                                    'published' => ['label' => 'yayında', 'dot' => 'var(--color-success)'],
                                ];
                                $state = $statusMap[$writing->status] ?? ['label' => $writing->status, 'dot' => 'var(--color-ink-subtle)'];
                            @endphp
                            <span class="inline-flex items-center gap-2 text-xs">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $state['dot'] }}"></span>
                                {{ $state['label'] }}
                            </span>
                        </td>
                        <td class="font-mono text-xs tabular-nums text-[var(--color-ink-muted)]">
                            {{ optional($writing->published_at)->format('Y-m-d') ?? '—' }}
                        </td>
                        <td class="font-mono text-xs text-[var(--color-ink-muted)]">{{ $writing->read_minutes }} dk</td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                @if (! $isTrashed)
                                    <a href="{{ $writing->url() }}" target="_blank" rel="noopener"
                                       class="btn btn--ghost btn--sm" title="Public'te göster">↗</a>

                                    @can('update', $writing)
                                        <a href="{{ route('admin.writings.edit', $writing) }}"
                                           class="btn btn--ghost btn--sm">Düzenle</a>
                                    @endcan

                                    @can('publish', $writing)
                                        @if ($writing->status !== 'published')
                                            <form method="POST" action="{{ route('admin.writings.publish', $writing) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn--sm" title="Şimdi yayımla">
                                                    Yayımla
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.writings.unpublish', $writing) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn--ghost btn--sm" title="Taslağa al">
                                                    Taslak
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('delete', $writing)
                                        <form method="POST" action="{{ route('admin.writings.destroy', $writing) }}" class="inline"
                                              onsubmit="return confirm('Bu yazıyı çöpe al?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn--ghost btn--sm" title="Sil">
                                                <span class="text-[var(--color-danger)]">×</span>
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-xs text-[var(--color-ink-subtle)]">çöpte</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $writings->links() }}
    </div>
@endif

@endsection
