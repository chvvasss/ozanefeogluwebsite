@extends('layouts.admin', ['title' => ($message->subject ?: 'Mesaj').' — '.$message->name])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.contact.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Mesajlar</a>
            &nbsp;·&nbsp; detay
        </p>
        <h1 class="admin-page-title" style="font-size: var(--text-2xl);">
            {{ $message->subject ?: '(konusuz)' }}
        </h1>
        <p class="admin-page-subtitle">
            {{ $message->name }} &lt;{{ $message->email }}&gt;
            &nbsp;·&nbsp; {{ $message->created_at->format('d F Y H:i') }}
        </p>
    </div>
</header>

<div class="grid gap-6 lg:grid-cols-[2fr_1fr] items-start">

    <div class="admin-card">
        <p class="admin-card-title">Mesaj</p>
        <div class="whitespace-pre-wrap text-[var(--text-md)] leading-relaxed text-[var(--color-ink)]">{{ $message->body }}</div>
    </div>

    <aside class="space-y-4">
        <div class="admin-card">
            <p class="admin-card-title">Durum</p>
            <form method="POST" action="{{ route('admin.contact.update', $message) }}" class="flex flex-col gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="input">
                    @foreach (App\Models\ContactMessage::STATUSES as $status)
                        <option value="{{ $status }}" @selected($message->status === $status)>
                            {{ match($status) {
                                'new' => 'yeni',
                                'read' => 'okundu',
                                'replied' => 'yanıtlandı',
                                'spam' => 'spam',
                                default => $status,
                            } }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn--sm self-start">Güncelle</button>
            </form>
        </div>

        <div class="admin-card">
            <p class="admin-card-title">Cevap</p>
            <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject ?? '') }}"
               class="btn btn--accent btn--sm">E-posta ile yanıtla ↗</a>
            <p class="field-hint mt-3">Yanıtladıktan sonra durumu "yanıtlandı" olarak işaretleyebilirsin.</p>
        </div>

        <div class="admin-card">
            <p class="admin-card-title">Meta</p>
            <dl class="font-mono text-xs space-y-2">
                <div><dt class="text-[var(--color-ink-subtle)]">IP</dt><dd>{{ $message->ip_address ?? '—' }}</dd></div>
                <div><dt class="text-[var(--color-ink-subtle)]">UA</dt><dd class="break-all">{{ $message->user_agent ?? '—' }}</dd></div>
                <div><dt class="text-[var(--color-ink-subtle)]">Okundu</dt><dd>{{ optional($message->read_at)->format('Y-m-d H:i') ?? 'henüz değil' }}</dd></div>
            </dl>
        </div>

        @can('delete', App\Models\Page::class)
            <form method="POST" action="{{ route('admin.contact.destroy', $message) }}"
                  onsubmit="return confirm('Bu mesajı sil?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost btn--sm text-[var(--color-danger)]">
                    Mesajı sil
                </button>
            </form>
        @endcan
    </aside>
</div>

@endsection
