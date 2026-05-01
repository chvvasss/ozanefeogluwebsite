<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">

    <title>{{ $title ?? 'Admin' }} · ozanefeoglu.com</title>

    {{-- Brand · Editorial Silence v2 (Yalın İmza) --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/branding/apple-touch-icon.svg">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#faf9f5">

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('_x_theme-pref');
                var pref = stored ? JSON.parse(stored) : 'light';
                if (pref === 'system') {
                    pref = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    localStorage.setItem('_x_theme-pref', JSON.stringify(pref));
                }
                if (pref !== 'light' && pref !== 'dark') pref = 'light';
                document.documentElement.dataset.theme = pref;
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="font-sans antialiased">

<div class="admin-shell" x-data="adminShell">
    @include('partials.admin-sidebar')

    <header class="admin-topbar">
        <button type="button"
                class="admin-sidebar-toggle btn btn--ghost btn--sm md:hidden"
                @click="toggleSidebar()"
                aria-label="Menüyü aç">
            ≡
        </button>

        <div class="flex items-center gap-4 ml-auto">
            <div x-data="themeToggle">
                <button type="button"
                        @click="cycle()"
                        class="btn btn--ghost btn--sm"
                        :title="'Tema: ' + preference">
                    <span x-show="resolved === 'light'" aria-hidden="true">◐</span>
                    <span x-show="resolved === 'dark'" aria-hidden="true">◑</span>
                    <span class="hidden md:inline" x-text="preference === 'light' ? 'Aydınlık' : 'Karanlık'"></span>
                </button>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn--ghost btn--sm">Çıkış</button>
            </form>
        </div>
    </header>

    <main class="admin-main">
        @if (session('status'))
            <div class="flash flash--success mb-6">{{ session('status') }}</div>
        @endif
        @if (session('warning'))
            <div class="flash flash--warning mb-6">{{ session('warning') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash--danger mb-6">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
