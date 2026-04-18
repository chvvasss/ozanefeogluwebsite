<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ozan Efeoğlu — Builds quietly ambitious software' }}</title>
    <meta name="description" content="{{ $description ?? 'Kişisel portfolyo ve yazı sahnesi — sessiz ama iddialı yazılım.' }}">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="alternate" type="application/rss+xml" href="/feed.xml" title="Writing — RSS">

    {{-- Pre-hydration theme — avoids FOUC --}}
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-[var(--color-ink)] focus:text-[var(--color-bg)] focus:px-4 focus:py-2 focus:rounded">
    İçeriğe geç
</a>

@include('partials.public-header')

<main id="main">
    {{ $slot ?? '' }}
    @yield('content')
</main>

@include('partials.public-footer')

</body>
</html>
