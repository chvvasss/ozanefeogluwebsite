<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">

    <title>{{ $title ?? 'Giriş' }} · ozanefeoglu.com</title>

    {{-- Brand · Editorial Silence v2 (Yalın İmza) --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/branding/apple-touch-icon.svg">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#faf9f5">

    <script>
        (function () {
            try {
                var pref = localStorage.getItem('_x_theme-pref');
                pref = pref ? JSON.parse(pref) : 'system';
                var sysDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var resolved = pref === 'system' ? (sysDark ? 'dark' : 'light') : pref;
                document.documentElement.dataset.theme = resolved;
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="font-sans antialiased">

<div class="auth-shell" data-mark="№ {{ now()->format('y.m.d') }} — MASA · OZANEFEOGLU.COM">
    <aside class="auth-pane auth-pane--aside" aria-hidden="true">
        <div class="relative z-10 flex flex-col h-full justify-between">
            <div class="flex items-center gap-3 text-[var(--color-paper-300)]">
                {{-- Editorial Silence mark · inverse for dark aside --}}
                <svg width="32" height="32" viewBox="0 0 64 64" aria-hidden="true">
                    <text x="6" y="48"
                          font-family="'Source Serif 4 Variable', 'Source Serif 4', Charter, Georgia, serif"
                          font-weight="600" font-size="48" letter-spacing="-1.6"
                          style="font-variation-settings: 'opsz' 48;"
                          fill="currentColor">oe</text>
                    <circle cx="49" cy="46" r="4.4" fill="#b91c1c"/>
                </svg>
                <span class="text-xs uppercase tracking-[0.2em]">Yazı masası · özel</span>
            </div>
            <p class="auth-marque">
                Sahadan <em>geldi</em>,<br>
                burada <em>oturuyor</em>.
            </p>
            <div class="flex items-baseline justify-between text-xs text-[var(--color-paper-400)] font-mono">
                <span>— yazı masası</span>
                <span>{{ now()->format('Y') }}</span>
            </div>
        </div>
    </aside>

    <section class="auth-pane">
        {{ $slot ?? '' }}
        @yield('content')
    </section>
</div>

</body>
</html>
