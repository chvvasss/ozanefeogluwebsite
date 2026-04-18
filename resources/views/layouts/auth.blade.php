<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">

    <title>{{ $title ?? 'Giriş' }} · ozanefeoglu.com</title>

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
            <div class="flex items-center gap-2 text-[var(--color-paper-300)]">
                <span class="inline-block w-2 h-2 rounded-full bg-[var(--color-ember-400)]"></span>
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
