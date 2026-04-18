@extends('layouts.admin', ['title' => 'Denetim kaydı'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Güvenlik · kayıtlar</p>
        <h1 class="admin-page-title">Denetim kaydı</h1>
        <p class="admin-page-subtitle">Her yönetim aksiyonu — kim, ne zaman, neyi değiştirdi.</p>
    </div>
</header>

<div class="admin-card p-0 overflow-hidden">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Zaman</th>
                <th>Kim</th>
                <th>Olay</th>
                <th>Detay</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="font-mono text-xs">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->causer?->name ?? 'sistem' }}</td>
                    <td>
                        <span class="font-mono text-xs px-2 py-1 rounded bg-[var(--color-bg-muted)]">
                            {{ $log->log_name }}.{{ $log->event ?? '-' }}
                        </span>
                    </td>
                    <td class="text-xs text-[var(--color-ink-muted)]">
                        @if ($log->properties)
                            <code class="font-mono text-[0.7rem] break-all">{{ json_encode($log->properties->toArray(), JSON_UNESCAPED_UNICODE) }}</code>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-12 text-[var(--color-ink-muted)] text-sm">
                    Henüz bir kayıt yok.
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
@endsection
