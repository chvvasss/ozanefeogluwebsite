@extends('layouts.admin', ['title' => 'Denetim kaydı'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Güvenlik · kayıtlar</p>
        <h1 class="admin-page-title">Denetim kaydı</h1>
        <p class="admin-page-subtitle">{{ $logs->total() }} kayıt — kim, ne zaman, neyi değiştirdi.</p>
    </div>
</header>

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.audit-log') }}"
      class="admin-card admin-filter-bar flex flex-wrap items-end gap-4 mb-6">
    <div class="field flex-1 min-w-[12rem]">
        <label for="q" class="field-label">Ara</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}"
               placeholder="açıklama içinde…" class="input">
    </div>

    <div class="field w-48">
        <label for="event" class="field-label">Olay</label>
        <select id="event" name="event" class="input">
            <option value="">hepsi</option>
            @foreach ($eventNames as $event)
                <option value="{{ $event }}" @selected(($filters['event'] ?? '') === $event)>{{ $event }}</option>
            @endforeach
        </select>
    </div>

    <div class="field w-48">
        <label for="subject" class="field-label">Konu türü</label>
        <select id="subject" name="subject" class="input">
            <option value="">hepsi</option>
            @foreach ($subjectTypes as $type)
                <option value="{{ $type }}" @selected(($filters['subject'] ?? '') === $type)>
                    {{ class_basename($type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field w-48">
        <label for="causer" class="field-label">Kullanıcı</label>
        <select id="causer" name="causer" class="input">
            <option value="">hepsi</option>
            @foreach ($causers as $causer)
                <option value="{{ $causer->id }}" @selected((string) ($filters['causer'] ?? '') === (string) $causer->id)>
                    {{ $causer->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field w-40">
        <label for="log" class="field-label">Kanal</label>
        <select id="log" name="log" class="input">
            <option value="">hepsi</option>
            @foreach ($logNames as $log)
                <option value="{{ $log }}" @selected(($filters['log'] ?? '') === $log)>{{ $log }}</option>
            @endforeach
        </select>
    </div>

    <div class="field w-40">
        <label for="from" class="field-label">Başlangıç</label>
        <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}" class="input">
    </div>

    <div class="field w-40">
        <label for="to" class="field-label">Bitiş</label>
        <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}" class="input">
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--sm">Süz</button>
        <a href="{{ route('admin.audit-log') }}" class="btn btn--ghost btn--sm">Temizle</a>
    </div>
</form>

<div class="admin-card p-0 overflow-hidden">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Zaman</th>
                <th>Kim</th>
                <th>Olay</th>
                <th>Konu</th>
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
                        @if ($log->subject_type)
                            <span class="font-mono">{{ class_basename($log->subject_type) }}#{{ $log->subject_id ?? '—' }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-xs text-[var(--color-ink-muted)]">
                        @if ($log->description)
                            <div class="mb-1">{{ $log->description }}</div>
                        @endif
                        @if ($log->properties && $log->properties->count() > 0)
                            <code class="font-mono text-[0.7rem] break-all">{{ json_encode($log->properties->toArray(), JSON_UNESCAPED_UNICODE) }}</code>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-12 text-[var(--color-ink-muted)] text-sm">
                    Kriterlere uyan kayıt yok.
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
@endsection
