@extends('layouts.admin', ['title' => 'Oturumlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Hesap · aktif cihazlar</p>
        <h1 class="admin-page-title">Oturumlar</h1>
        <p class="admin-page-subtitle">Hangi cihazda açıksın, nerede, en son ne zaman.</p>
    </div>
</header>

@if ($devices->isEmpty())
    <div class="admin-card text-center py-12">
        <p class="text-sm text-[var(--color-ink-muted)]">Hiç aktif cihaz yok.</p>
    </div>
@else
    <div class="admin-card p-0 overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Cihaz</th>
                    <th>IP</th>
                    <th>Son aktivite</th>
                    <th class="text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $device)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($device->session_id === $currentSessionId)
                                    <span class="w-2 h-2 rounded-full bg-[var(--color-success)]"
                                          title="Bu oturum"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-[var(--color-rule-strong)]"></span>
                                @endif
                                <span>{{ $device->device_label ?? 'Bilinmeyen' }}</span>
                                @if ($device->session_id === $currentSessionId)
                                    <span class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)]">bu cihaz</span>
                                @endif
                            </div>
                        </td>
                        <td class="font-mono text-xs">{{ $device->ip_address ?? '—' }}</td>
                        <td class="font-mono text-xs">{{ optional($device->last_active_at)->diffForHumans() ?? '—' }}</td>
                        <td class="text-right">
                            @if ($device->session_id !== $currentSessionId)
                                <form method="POST" action="{{ route('admin.sessions.destroy', $device) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn--ghost btn--sm text-[var(--color-danger)]"
                                            onclick="return confirm('Bu oturumu kapat?')">
                                        Kapat
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-[var(--color-ink-subtle)]">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
