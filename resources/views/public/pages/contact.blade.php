@extends('layouts.public', [
    'title' => ($page->meta_title ?: $page->title).' · Ozan Efeoğlu',
    'description' => $page->meta_description ?: $page->intro,
])

@section('content')

@php
    $channels = collect($page->extra('channels', []));
    $primaryChannel = $channels->firstWhere('primary', true) ?? $channels->first();
    $secondaryChannels = $channels->reject(fn ($c) => ($c['primary'] ?? false) === true)->values();
    $pgp = $page->extra('pgp', []);
    $disclosure = $page->extra('disclosure', []);
    $responseHours = $page->extra('response_time_hours', 72);
    $accreditation = $page->extra('accreditation');
@endphp

<article class="contact-page">

    {{-- =========================== HERO =========================== --}}
    <header class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-12 md:pt-20 pb-10 md:pb-14">
        <p class="eyebrow reveal reveal-1 mb-5">İletişim · güvenli kanallar</p>
        <h1 class="display-fraunces reveal reveal-2 leading-[0.98] max-w-[20ch]"
            style="font-size: clamp(2.5rem, 6.5vw, 5rem); letter-spacing: var(--tracking-tightest);">
            Üç <em class="italic text-[var(--color-accent)]">güvenli</em> kanal.
        </h1>
        @if ($page->intro)
            <p class="mt-6 max-w-[52ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)] reveal reveal-3">
                {{ $page->intro }}
            </p>
        @endif
    </header>

    {{-- =========================== CHANNELS GRID =========================== --}}
    <section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] border-y border-[var(--color-rule)] py-8 md:py-10">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($channels as $ch)
                <div class="channel-card {{ ($ch['primary'] ?? false) ? 'channel-card--primary' : '' }}">
                    <div class="channel-type">
                        @switch($ch['type'])
                            @case('email')  <span aria-hidden="true">@</span>  @break
                            @case('signal') <span aria-hidden="true">▲</span>  @break
                            @case('pgp')    <span aria-hidden="true">⚿</span>  @break
                            @default        <span aria-hidden="true">·</span>
                        @endswitch
                        <span>{{ $ch['label'] }}</span>
                    </div>
                    <div class="channel-handle" x-data="copyHandle('{{ e($ch['handle']) }}')">
                        <code x-ref="handle">{{ $ch['handle'] }}</code>
                        <button type="button" @click="copy()" class="channel-copy"
                                :aria-label="copied ? 'Kopyalandı' : 'Kopyala'"
                                :class="copied && 'is-copied'">
                            <span x-text="copied ? '✓' : '⎘'"></span>
                        </button>
                    </div>
                    <p class="channel-note">{{ $ch['note'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- =========================== FORM + PGP =========================== --}}
    <section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-16 md:py-24 grid gap-12 lg:grid-cols-[1.6fr_1fr]">

        {{-- Message form --}}
        <div id="mesaj">
            <p class="eyebrow mb-3">Mesaj bırak</p>
            <h2 class="display-fraunces text-[clamp(1.75rem,3vw,2.75rem)] leading-[1.05] mb-2"
                style="letter-spacing: var(--tracking-tighter);">
                Kurumsal değilse, doğrudan <em class="italic text-[var(--color-accent)]">buradan</em>.
            </h2>
            <p class="text-sm text-[var(--color-ink-muted)] max-w-[56ch] mb-8">
                Hassas kaynaklar için lütfen <a href="#mesaj" class="underline decoration-[var(--color-accent)] underline-offset-4">Signal veya PGP</a> tercih et.
                Bu form şifrelenmemiştir; yalnızca editör-basın temasları için.
            </p>

            @if (session('contact_status'))
                <div id="tesekkur" class="flash flash--success mb-6" role="status">
                    <span aria-hidden="true">✓</span>
                    <span>{{ session('contact_status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="flash flash--danger mb-6" role="alert">
                    <ul class="list-disc pl-4 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
                @csrf

                {{-- Honeypot (hidden to humans, filled by bots) --}}
                <div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label for="cf-name" class="field-label">İsim</label>
                        <input id="cf-name" name="name" type="text" required maxlength="120"
                               value="{{ old('name') }}" class="input">
                    </div>
                    <div class="field">
                        <label for="cf-email" class="field-label">E-posta</label>
                        <input id="cf-email" name="email" type="email" required maxlength="180"
                               value="{{ old('email') }}" class="input">
                    </div>
                </div>

                <div class="field">
                    <label for="cf-subject" class="field-label">Konu (opsiyonel)</label>
                    <input id="cf-subject" name="subject" type="text" maxlength="200"
                           value="{{ old('subject') }}" class="input"
                           placeholder="Ör. yayın teklifi, röportaj talebi…">
                </div>

                <div class="field">
                    <label for="cf-body" class="field-label">Mesaj</label>
                    <textarea id="cf-body" name="body" rows="8" required maxlength="8000"
                              class="input resize-y" placeholder="Konuyu ve aciliyeti kısaca özetle.">{{ old('body') }}</textarea>
                    <p class="field-hint">Kaynak isimleri, konum ve zamanlar bu form yerine güvenli kanala yazılmalı.</p>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
                    <p class="text-xs text-[var(--color-ink-subtle)] font-mono">
                        ~ {{ $responseHours }} saat içinde yanıt
                    </p>
                    <button type="submit" class="btn btn--accent">
                        Gönder <span aria-hidden="true">→</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar: PGP + body + disclosure --}}
        <aside class="space-y-8 lg:sticky lg:top-28 lg:self-start">

            @if ($page->body)
                <div class="prose-note">
                    {!! $page->body !!}
                </div>
            @endif

            @if (! empty($pgp))
                <div class="pgp-box" x-data="{ expanded: false }">
                    <div class="pgp-header">
                        <span class="eyebrow">PGP anahtarı</span>
                        @if (! empty($pgp['key_id']))
                            <span class="font-mono text-xs text-[var(--color-ink-subtle)]">{{ $pgp['key_id'] }}</span>
                        @endif
                    </div>
                    @if (! empty($pgp['fingerprint']))
                        <code class="pgp-fingerprint">{{ $pgp['fingerprint'] }}</code>
                    @endif
                    <div class="pgp-actions">
                        @if (! empty($pgp['download']))
                            <a href="{{ $pgp['download'] }}" class="btn btn--ghost btn--sm">Anahtarı indir (.asc)</a>
                        @endif
                    </div>
                </div>
            @endif

            @if (! empty($disclosure))
                <div class="disclosure-box">
                    <p class="eyebrow mb-3">Kaynak güvenliği</p>
                    <ul class="space-y-3 text-sm text-[var(--color-ink-muted)] leading-relaxed">
                        @foreach ($disclosure as $line)
                            <li class="flex gap-2">
                                <span class="text-[var(--color-accent)] mt-1" aria-hidden="true">◆</span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($accreditation)
                <div class="text-xs font-mono text-[var(--color-ink-subtle)] border-t border-[var(--color-rule)] pt-4">
                    {{ $accreditation }}
                </div>
            @endif
        </aside>

    </section>

</article>

@endsection
