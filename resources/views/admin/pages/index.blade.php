@extends('layouts.admin', ['title' => 'Sayfalar'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Yayın · sayfalar</p>
        <h1 class="admin-page-title">Sayfalar</h1>
        <p class="admin-page-subtitle">Hakkımda, İletişim ve diğer statik sayfalar — içerikleri buradan düzenle.</p>
    </div>
    @can('create', App\Models\Page::class)
        <a href="{{ route('admin.pages.create') }}" class="btn btn--accent">+ Yeni sayfa</a>
    @endcan
</header>

<div class="admin-card p-0 overflow-hidden">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th class="w-24">Tip</th>
                <th class="w-28">Şablon</th>
                <th class="w-28">Durum</th>
                <th class="text-right w-40">İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>
                        <a href="{{ route('admin.pages.edit', $page) }}"
                           class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)] font-medium">
                            {{ $page->getTranslation('title', 'tr', false) ?: '(başlıksız)' }}
                        </a>
                        <div class="text-xs text-[var(--color-ink-subtle)] font-mono mt-1">
                            /{{ $page->slug }}
                        </div>
                    </td>
                    <td>
                        @if ($page->kind === 'system')
                            <span class="inline-flex items-center gap-1.5 font-mono text-[0.65rem] uppercase tracking-[0.16em] text-[var(--color-accent)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-accent)]"></span> sistem
                            </span>
                        @else
                            <span class="inline-flex items-center font-mono text-[0.65rem] uppercase tracking-[0.16em] text-[var(--color-ink-muted)]">
                                özel
                            </span>
                        @endif
                    </td>
                    <td class="font-mono text-xs">{{ $page->template }}</td>
                    <td>
                        @if ($page->is_published)
                            <span class="inline-flex items-center gap-2 text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span> yayında
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 text-xs text-[var(--color-ink-subtle)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-ink-subtle)]"></span> gizli
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ $page->url() }}" target="_blank" rel="noopener"
                               class="btn btn--ghost btn--sm" title="Public'te aç">↗</a>
                            @can('update', $page)
                                <a href="{{ route('admin.pages.edit', $page) }}"
                                   class="btn btn--ghost btn--sm">Düzenle</a>
                            @endcan
                            @can('delete', $page)
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline"
                                      onsubmit="return confirm('Bu sayfayı sil?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--ghost btn--sm text-[var(--color-danger)]">×</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $pages->links() }}</div>

@endsection
