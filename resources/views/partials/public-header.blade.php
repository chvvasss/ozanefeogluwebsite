{{--
    Public header — Field Dossier v2 (ADR-016, Session 3.5).
    Tek button ailesi: .btn (primary) / .btn--secondary / .icon-btn / .link-quiet.
    Drawer = self-contained full-screen overlay (no magic header-h).
    Drawer typography sakin: aynı sans-serif aile, body weight, padlama hairline ayraç.
--}}
<header
    x-data="{ scrolled: false, drawerOpen: false }"
    x-init="
        window.addEventListener('scroll', () => scrolled = window.scrollY > 24, { passive: true });
        $watch('drawerOpen', v => document.documentElement.classList.toggle('is-locked', v));
    "
    @keydown.escape.window="drawerOpen = false"
    :class="scrolled ? 'py-3 border-b border-[var(--color-rule)]' : 'py-5'"
    class="sticky top-0 z-20 backdrop-blur-md bg-[color-mix(in_oklch,var(--color-bg)_88%,transparent)] transition-[padding,border-color] duration-200"
>
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] flex items-center justify-between gap-6">

        {{-- Wordmark --}}
        <a href="{{ route('home') }}" class="no-underline group inline-flex flex-col leading-none">
            <span class="font-[var(--font-display)] font-semibold text-[var(--text-md)] text-[var(--color-ink)] tracking-[var(--tracking-tight)]">
                {{ site_setting('identity.name') }}
            </span>
            <span class="mt-1 font-mono text-[0.6rem] tracking-[0.22em] uppercase text-[var(--color-ink-subtle)]">
                {{ strtoupper(site_setting('identity.base')) }}
            </span>
        </a>

        {{-- Primary nav (md+) --}}
        <nav class="hidden md:flex items-center gap-7 text-sm" aria-label="Ana navigasyon">
            <a href="{{ route('writing.index') }}" class="nav-link"
               aria-current="{{ request()->routeIs('writing.*') ? 'page' : 'false' }}">Yazılar</a>

            @if (site_setting('nav.show_visuals') && Route::has('visuals.index'))
                <a href="{{ route('visuals.index') }}" class="nav-link"
                   aria-current="{{ request()->routeIs('visuals.*') ? 'page' : 'false' }}">Görüntü</a>
            @endif

            <a href="{{ route('about') }}" class="nav-link"
               aria-current="{{ request()->routeIs('about') ? 'page' : 'false' }}">Hakkında</a>
            <a href="{{ route('contact') }}" class="nav-link"
               aria-current="{{ request()->routeIs('contact*') ? 'page' : 'false' }}">İletişim</a>
        </nav>

        {{-- Utilities — tek aile chrome --}}
        <div class="flex items-center gap-2">
            <div x-data="themeToggle" class="hidden sm:block">
                <button type="button"
                        @click="cycle()"
                        class="icon-btn"
                        :title="'Tema: ' + preference"
                        :aria-label="'Tema değiştir. Şu an: ' + resolved">
                    <span x-show="resolved === 'light'" aria-hidden="true">◐</span>
                    <span x-show="resolved === 'dark'" aria-hidden="true">◑</span>
                </button>
            </div>

            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn--sm hidden sm:inline-flex">
                    Masa <span aria-hidden="true" class="opacity-60">↗</span>
                </a>
            @endauth

            <button
                @click="drawerOpen = !drawerOpen"
                class="icon-btn md:hidden"
                :aria-expanded="drawerOpen.toString()"
                aria-controls="mobile-drawer"
                :aria-label="drawerOpen ? 'Menüyü kapat' : 'Menüyü aç'"
            >
                <span x-show="!drawerOpen" aria-hidden="true">≡</span>
                <span x-show="drawerOpen" aria-hidden="true">×</span>
            </button>
        </div>
    </div>

    {{-- =================== MOBILE DRAWER ===================
         Self-contained overlay. Kendi mini-header'ı var (wordmark + close).
         Tipografi sakin: sans-serif body weight, hairline ayraçlar, dot-eyebrow
         aktif state. Hiç magic number yok. --}}
    <div
        id="mobile-drawer"
        x-show="drawerOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 z-40 bg-[var(--color-bg)] flex flex-col overscroll-contain"
        role="dialog"
        aria-modal="true"
        aria-label="Ana menü"
        x-cloak
    >
        {{-- Drawer mini-header — same wordmark, close button --}}
        <div class="flex items-center justify-between px-[clamp(1.5rem,6vw,2.5rem)] py-5 border-b border-[var(--color-rule)]">
            <a href="{{ route('home') }}" @click="drawerOpen = false"
               class="no-underline inline-flex flex-col leading-none">
                <span class="font-[var(--font-display)] font-semibold text-[var(--text-md)] text-[var(--color-ink)]">
                    {{ site_setting('identity.name') }}
                </span>
                <span class="mt-1 font-mono text-[0.6rem] tracking-[0.22em] uppercase text-[var(--color-ink-subtle)]">
                    {{ strtoupper(site_setting('identity.base')) }}
                </span>
            </a>
            <button type="button" @click="drawerOpen = false" class="icon-btn" aria-label="Menüyü kapat">
                <span aria-hidden="true">×</span>
            </button>
        </div>

        {{-- Nav body --}}
        <nav class="flex-1 px-[clamp(1.5rem,6vw,2.5rem)] py-4 overflow-y-auto" aria-label="Mobil navigasyon">
            <p class="eyebrow mt-4 mb-2">Sayfalar</p>
            <a href="{{ route('writing.index') }}" @click="drawerOpen = false"
               class="drawer-link"
               aria-current="{{ request()->routeIs('writing.*') ? 'page' : 'false' }}">
                <span>Yazılar</span>
                <span class="dateline" aria-hidden="true">→</span>
            </a>

            @if (site_setting('nav.show_visuals') && Route::has('visuals.index'))
                <a href="{{ route('visuals.index') }}" @click="drawerOpen = false"
                   class="drawer-link">
                    <span>Görüntü</span>
                    <span class="dateline" aria-hidden="true">→</span>
                </a>
            @endif

            <a href="{{ route('about') }}" @click="drawerOpen = false"
               class="drawer-link"
               aria-current="{{ request()->routeIs('about') ? 'page' : 'false' }}">
                <span>Hakkında</span>
                <span class="dateline" aria-hidden="true">→</span>
            </a>

            <a href="{{ route('contact') }}" @click="drawerOpen = false"
               class="drawer-link"
               aria-current="{{ request()->routeIs('contact*') ? 'page' : 'false' }}">
                <span>İletişim</span>
                <span class="dateline" aria-hidden="true">→</span>
            </a>
        </nav>

        {{-- Drawer footer utilities — tek aile chrome --}}
        <div class="px-[clamp(1.5rem,6vw,2.5rem)] py-5 border-t border-[var(--color-rule)] flex items-center justify-between gap-3">
            <div x-data="themeToggle">
                <button type="button"
                        @click="cycle()"
                        class="btn btn--secondary btn--sm"
                        :aria-label="'Tema değiştir. Şu an: ' + resolved">
                    <span x-show="resolved === 'light'" aria-hidden="true">◐</span>
                    <span x-show="resolved === 'dark'" aria-hidden="true">◑</span>
                    <span x-text="resolved === 'dark' ? 'Karanlık' : 'Aydınlık'"></span>
                </button>
            </div>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn--sm">
                    Masa <span aria-hidden="true" class="opacity-60">↗</span>
                </a>
            @endauth
        </div>
    </div>
</header>
