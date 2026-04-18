@extends('layouts.admin', ['title' => 'İletişim mesajları'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Gelen kutusu</p>
        <h1 class="admin-page-title">Mesajlar</h1>
        <p class="admin-page-subtitle">
            {{ $counts['new'] }} yeni · {{ $counts['read'] }} okundu · {{ $counts['replied'] }} yanıtlandı
            @if ($counts['spam'] > 0) · {{ $counts['spam'] }} spam @endif
        </p>
    </div>
</header>

<nav class="mb-4 flex flex-wrap gap-2" aria-label="Durum filtresi">
    @foreach (['' => 'hepsi', 'new' => 'yeni', 'read' => 'okundu', 'replied' => 'yanıtlandı', 'spam' => 'spam'] as $value => $label)
        @php $active = (string) $filter === $value; @endphp
        <a href="{{ route('admin.contact.index', $value === '' ? [] : ['status' => $value]) }}"
           class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs uppercase tracking-[0.15em] no-underline
                  {{ $active
                        ? 'border-[var(--color-ink)] bg-[var(--color-ink)] text-[var(--color-bg)]'
                        : 'border-[var(--color-rule-strong)] text-[var(--color-ink-muted)] hover:border-[var(--color-ink)] hover:text-[var(--color-ink)]' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>

@if ($messages->isEmpty())
    <div class="admin-card text-center py-14">
        <p class="text-sm text-[var(--color-ink-muted)]">Bu filtrede mesaj yok.</p>
    </div>
@else
    <div class="admin-card p-0 overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="w-28">Tarih</th>
                    <th class="w-44">Gönderen</th>
                    <th>Konu</th>
                    <th class="w-24">Durum</th>
                    <th class="text-right w-28">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr class="{{ $message->status === 'new' ? 'font-semibold' : '' }}">
                        <td class="font-mono text-xs tabular-nums text-[var(--color-ink-muted)]">
                            {{ $message->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            <div>{{ $message->name }}</div>
                            <div class="text-xs text-[var(--color-ink-subtle)] font-mono">{{ $message->email }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.contact.show', $message) }}"
                               class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)]">
                                {{ $message->subject ?? \Illuminate\Support\Str::limit($message->body, 60) }}
                            </a>
                        </td>
                        <td>
                            @php
                                $badge = match ($message->status) {
                                    'new'     => ['label' => 'yeni',       'dot' => 'var(--color-accent)'],
                                    'read'    => ['label' => 'okundu',     'dot' => 'var(--color-ink-subtle)'],
                                    'replied' => ['label' => 'yanıtlandı', 'dot' => 'var(--color-success)'],
                                    'spam'    => ['label' => 'spam',       'dot' => 'var(--color-danger)'],
                                    default   => ['label' => $message->status, 'dot' => 'var(--color-ink-subtle)'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-2 text-xs">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.contact.show', $message) }}" class="btn btn--ghost btn--sm">Aç</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endif

@endsection
