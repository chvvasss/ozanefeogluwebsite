<header
    x-data="{ scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 24, { passive: true })"
    :class="scrolled ? 'py-3 border-b border-[var(--color-rule)]' : 'py-6'"
    class="sticky top-0 z-20 backdrop-blur-md bg-[color-mix(in_oklch,var(--color-bg)_88%,transparent)] transition-[padding,border-color] duration-300"
>
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] flex items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="inline-flex items-baseline gap-2 no-underline">
            <span class="inline-block w-2 h-2 rounded-full bg-[var(--color-accent)]"></span>
            <span class="display-fraunces text-[var(--text-lg)]">Ozan Efeoğlu</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm" aria-label="Ana navigasyon">
            <a href="{{ route('writing.index') }}" class="nav-link"
               aria-current="{{ request()->routeIs('writing.*') ? 'page' : 'false' }}">Yazılar</a>
            <a href="{{ route('about') }}" class="nav-link"
               aria-current="{{ request()->routeIs('about') ? 'page' : 'false' }}">Hakkında</a>
            <a href="{{ route('contact') }}" class="nav-link"
               aria-current="{{ request()->routeIs('contact*') ? 'page' : 'false' }}">İletişim</a>
        </nav>

        <div class="flex items-center gap-2">
            <div x-data="themeToggle" class="hidden sm:block">
                <button type="button"
                        @click="cycle()"
                        class="btn btn--ghost btn--sm"
                        :title="'Tema: ' + preference"
                        :aria-label="'Tema değiştir. Şu an: ' + resolved">
                    <span x-show="resolved === 'light'" aria-hidden="true">◐</span>
                    <span x-show="resolved === 'dark'" aria-hidden="true">◑</span>
                </button>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="btn btn--sm hidden sm:inline-flex">
                Masa <span aria-hidden="true" class="opacity-60">↗</span>
            </a>

            <button
                x-data="mobileNav"
                @click="toggle()"
                class="btn btn--ghost btn--sm md:hidden"
                aria-label="Menüyü aç"
            >
                <span x-show="!open">≡</span>
                <span x-show="open">×</span>
            </button>
        </div>
    </div>
</header>
