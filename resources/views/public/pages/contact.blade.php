@php
    $rawTitle = $page->meta_title ?: $page->title;
    $siteName = site_setting('identity.name');
    $finalTitle = str_contains((string) $rawTitle, (string) $siteName) ? $rawTitle : $rawTitle.' · '.$siteName;
@endphp
@extends('layouts.public', [
    'title' => $finalTitle,
    'description' => $page->meta_description ?: $page->intro,
])

@section('content')

@php
    /** @var \App\Models\Page $page */

    $email          = site_setting('contact.email');
    $signalUrl      = site_setting('contact.signal_url');
    $pgpFingerprint = site_setting('contact.pgp_fingerprint');
    $pgpKeyId       = site_setting('contact.pgp_key_id');
    $pgpDownload    = site_setting('contact.pgp_download');
    $responseNote   = $page->extra('response_note');
@endphp

<article>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 1 — POSTER: "Yazın."
         Typographic front moment. Single composition, no grid, no cards.
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--overture">
        <div class="page-wrap">
            <div class="contact-poster">
                <p class="eyebrow">İletişim</p>

                <h1 class="contact-poster-mark">Yazın.</h1>

                @if ($email)
                    <p>
                        <a href="mailto:{{ $email }}" class="contact-poster-mail">
                            {{ $email }}
                        </a>
                    </p>
                @endif

                @if ($page->intro)
                    <p class="contact-poster-note">{{ $page->intro }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 2 — FORM
         Inline form below the poster. No sidebar, no card chrome.
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--muted border-t border-[var(--color-rule)]" id="mesaj">
        <div class="page-wrap-narrow">
            <header class="mb-8">
                <p class="eyebrow mb-3">Mesaj</p>
                <h2 class="display-editorial">Doğrudan yazın</h2>
                <p class="mt-4 text-sm text-[var(--color-ink-muted)] max-w-[60ch]">
                    Bu form şifrelenmemiştir. Kurumsal olmayan temaslar ve yayın önerileri içindir; hassas konular için güvenli kanalları kullanın.
                    <strong class="text-[var(--color-ink)]">Anadolu Ajansı kurumsal talepleri</strong> için doğrudan ajansın editör masasına yazın.
                </p>
                <p class="mt-3 text-xs text-[var(--color-ink-subtle)] max-w-[60ch]">
                    Mesajınızı gönderdiğinizde <a href="{{ route('legal.kvkk') }}" class="link-quiet">KVKK aydınlatma metnini</a> okumuş sayılırsınız. IP ve tarayıcı imzası kaydedilmez; mesajlar 90 gün sonra otomatik silinir.
                </p>
            </header>

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

                {{-- Honeypot --}}
                <div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label for="cf-name" class="field-label">İsim</label>
                        <input id="cf-name" name="name" type="text" required aria-required="true" maxlength="120"
                               value="{{ old('name') }}" class="input">
                    </div>
                    <div class="field">
                        <label for="cf-email" class="field-label">E-posta</label>
                        <input id="cf-email" name="email" type="email" required aria-required="true" maxlength="180"
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
                    <textarea id="cf-body" name="body" rows="8" required aria-required="true" maxlength="8000"
                              class="input resize-y"
                              placeholder="Konuyu ve aciliyeti kısaca özetleyin.">{{ old('body') }}</textarea>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
                    @if ($responseNote)
                        <p class="text-xs text-[var(--color-ink-subtle)] font-mono">
                            {{ $responseNote }}
                        </p>
                    @endif
                    <button type="submit" class="btn">
                        Gönder <span aria-hidden="true">→</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 3 — SECURE CHANNELS (config-driven, render only if real)
         Signal + PGP rendered ONLY when real values exist in config.
         No placeholder, no "yakında", no fake handle.
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($signalUrl || $pgpFingerprint)
        <section class="scene scene--tight border-t border-[var(--color-rule)]">
            <div class="page-wrap-narrow">
                <header class="mb-6">
                    <p class="eyebrow">Güvenli kanallar</p>
                    <h2 class="display-quiet mt-2">Hassas temas için</h2>
                </header>

                @if ($signalUrl)
                    <div class="mb-6">
                        <p class="kicker mb-2">Signal</p>
                        <a href="{{ $signalUrl }}" rel="noopener" class="font-mono text-[var(--text-md)] link-quiet break-all">
                            {{ $signalUrl }}
                        </a>
                        <p class="mt-2 text-sm text-[var(--color-ink-muted)] max-w-[52ch]">
                            Uçtan uca şifrelenir. Hassas konular için tercih edilir.
                        </p>
                    </div>
                @endif

                @if ($pgpFingerprint)
                    <div class="pgp-box">
                        <div class="pgp-header">
                            <span class="eyebrow">PGP</span>
                            @if ($pgpKeyId)
                                <span class="font-mono text-xs text-[var(--color-ink-subtle)]">{{ $pgpKeyId }}</span>
                            @endif
                        </div>
                        <code class="pgp-fingerprint">{{ $pgpFingerprint }}</code>
                        @if ($pgpDownload)
                            <div class="pgp-actions">
                                <a href="{{ $pgpDownload }}" class="btn btn--secondary btn--sm">
                                    Anahtarı indir (.asc)
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 4 — DISCLOSURE
         Single italic line, closing rhythm.
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($page->body)
        <section class="scene scene--closing border-t border-[var(--color-rule)]">
            <div class="page-wrap-narrow prose-note">
                {!! $page->body !!}
            </div>
        </section>
    @endif

</article>

@endsection
