@extends('layouts.admin', ['title' => 'Yayınlar'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Yayın · arşiv</p>
        <h1 class="admin-page-title">Yayınlar</h1>
        <p class="admin-page-subtitle">{{ $publications->total() }} kayıt</p>
    </div>
    @can('create', App\Models\Publication::class)
        <a href="{{ route('admin.publications.create') }}" class="btn btn--accent">+ Yeni yayın</a>
    @endcan
</header>

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.publications.index') }}"
      class="admin-card admin-filter-bar flex flex-wrap items-end gap-4 mb-6">
    <div class="field flex-1 min-w-[12rem]">
        <label for="q" class="field-label">Ara</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}"
               placeholder="yayın adı…" class="input">
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--sm">Süz</button>
        <a href="{{ route('admin.publications.index') }}" class="btn btn--ghost btn--sm">Temizle</a>
    </div>
</form>

@if ($publications->isEmpty())
    <div class="admin-card text-center py-16">
        <p class="display-fraunces text-2xl mb-2">Henüz yayın yok.</p>
        <p class="text-sm text-[var(--color-ink-muted)] mb-6">
            İlk yayın organını eklemek için sağ üstteki "Yeni yayın" düğmesini kullan.
        </p>
        @can('create', App\Models\Publication::class)
            <a href="{{ route('admin.publications.create') }}" class="btn btn--accent">+ Yeni yayın</a>
        @endcan
    </div>
@else
    <div class="admin-card p-0 overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ad</th>
                    <th class="w-24">Sıra</th>
                    <th class="w-28">Yazı</th>
                    <th>Web</th>
                    <th class="text-right w-40">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($publications as $publication)
                    <tr>
                        <td>
                            <a href="{{ route('admin.publications.edit', $publication) }}"
                               class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)] font-medium">
                                {{ $publication->name }}
                            </a>
                            <div class="text-xs text-[var(--color-ink-subtle)] font-mono mt-1">
                                /{{ $publication->slug }}
                            </div>
                        </td>
                        <td class="font-mono text-xs tabular-nums text-[var(--color-ink-muted)]">
                            {{ $publication->sort_order }}
                        </td>
                        <td class="font-mono text-xs text-[var(--color-ink-muted)]">
                            {{ $publication->writings_count }}
                        </td>
                        <td>
                            @if ($publication->url)
                                <a href="{{ $publication->url }}" target="_blank" rel="noopener"
                                   class="text-xs font-mono text-[var(--color-ink-muted)] hover:text-[var(--color-accent)] no-underline break-all">
                                    {{ Str::limit($publication->url, 44) }} ↗
                                </a>
                            @else
                                <span class="text-xs text-[var(--color-ink-subtle)]">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                @can('update', $publication)
                                    <a href="{{ route('admin.publications.edit', $publication) }}"
                                       class="btn btn--ghost btn--sm">Düzenle</a>
                                @endcan

                                @can('delete', $publication)
                                    <form method="POST" action="{{ route('admin.publications.destroy', $publication) }}" class="inline"
                                          onsubmit="return confirm('Bu yayını sil? Bağlı yazılardaki eşleşmeler kaldırılır.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--ghost btn--sm" title="Sil">
                                            <span class="text-[var(--color-danger)]">×</span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $publications->links() }}
    </div>
@endif

@endsection
