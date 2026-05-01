{{--
    Public footer — Field Dossier v3 (Emergency Rebuild).
    TRUTH ONLY. Config-null kanallar + disabled feature flags → hiç render yok.
--}}
@php
    $email   = site_setting('contact.email');
    $feed    = site_setting('features.feed_enabled');
    $showVisuals = site_setting('nav.show_visuals') && Route::has('visuals.index');

    $socialCandidates = [
        'mastodon'  => site_setting('social.mastodon_url'),
        'bluesky'   => site_setting('social.bluesky_url'),
        'x'         => site_setting('social.x_url'),
        'instagram' => site_setting('social.instagram_url'),
        'linkedin'  => site_setting('social.linkedin_url'),
        'github'    => site_setting('social.github_url'),
    ];
    $social = collect($socialCandidates)
        ->filter(fn ($url) => ! empty($url));
@endphp

<footer class="mt-[var(--space-section)] border-t border-[var(--color-rule)]">
    <div class="page-wrap py-12 grid gap-10 md:grid-cols-[2fr_1fr_1fr] items-start">

        {{-- Identity column --}}
        <div>
            <a href="{{ route('home') }}" class="no-underline inline-flex flex-col leading-none">
                <span class="font-[var(--font-display)] font-semibold text-[var(--text-md)] text-[var(--color-ink)]">
                    {{ site_setting('identity.name') }}
                </span>
                <span class="mt-1 font-mono text-[0.6rem] tracking-[0.22em] uppercase text-[var(--color-ink-subtle)]">
                    {{ strtoupper(site_setting('identity.base')) }}
                </span>
            </a>
            <p class="mt-4 text-sm text-[var(--color-ink-muted)] max-w-[44ch] leading-relaxed">
                {{ site_setting('identity.description') }}
            </p>
        </div>

        {{-- Quick nav --}}
        <div>
            <p class="eyebrow mb-3">Sayfalar</p>
            <ul class="space-y-1.5 text-sm">
                <li><a href="{{ route('writing.index') }}" class="link-quiet">Yazılar</a></li>
                @if ($showVisuals)
                    <li><a href="{{ route('visuals.index') }}" class="link-quiet">Görüntü</a></li>
                @endif
                <li><a href="{{ route('about') }}" class="link-quiet">Hakkında</a></li>
                <li><a href="{{ route('contact') }}" class="link-quiet">İletişim</a></li>
                <li><a href="{{ route('legal.show', ['slug' => 'kvkk']) }}" class="link-quiet">KVKK</a></li>
                <li><a href="{{ route('legal.show', ['slug' => 'gizlilik']) }}" class="link-quiet">Gizlilik</a></li>
                <li><a href="{{ route('legal.show', ['slug' => 'kunye']) }}" class="link-quiet">Künye</a></li>
                @if ($feed)
                    <li><a href="/feed.xml" class="link-quiet">RSS</a></li>
                @endif
            </ul>
        </div>

        {{-- Real-only contact column --}}
        <div>
            <p class="eyebrow mb-3">İletişim</p>
            <ul class="space-y-1.5 text-sm">
                @if ($email)
                    <li>
                        <a href="mailto:{{ $email }}" class="link-quiet font-mono text-[0.85rem]">
                            {{ $email }}
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('contact') }}" class="link-quiet">
                        Tüm kanallar →
                    </a>
                </li>
            </ul>

            @if ($social->isNotEmpty())
                <p class="eyebrow mt-6 mb-3">Açık hesaplar</p>
                <ul class="space-y-1.5 text-sm">
                    @foreach ($social as $key => $url)
                        <li>
                            <a href="{{ $url }}" rel="me noopener" class="link-quiet">
                                {{ ucfirst($key) }} ↗
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="page-wrap py-5 flex flex-wrap items-center justify-between gap-3 text-xs font-mono text-[var(--color-ink-subtle)] border-t border-[var(--color-rule)]">
        <span>© {{ now()->format('Y') }} {{ site_setting('identity.name') }}</span>
        <span class="flex items-center gap-4">
            <a href="/branding/system/" target="_blank" rel="noopener"
               class="uppercase tracking-[0.2em] hover:text-[var(--color-ink)] transition-colors no-underline"
               title="Editorial Silence — marka rehberi">Kolofon</a>
            <span class="uppercase tracking-[0.2em]">{{ strtoupper(site_setting('identity.base')) }}</span>
        </span>
    </div>
</footer>
