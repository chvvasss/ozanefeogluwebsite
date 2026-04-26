<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $title ?? site_setting('identity.name').' — '.site_setting('identity.role_primary');
        $pageDesc  = $description ?? site_setting('identity.description');
        $pageOgImage = $ogImage ?? site_setting('seo.og_image_url') ?? null;
        $pageOgType = $ogType ?? 'website';
        $pageCanonical = $canonical ?? url()->current();
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $pageCanonical }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $pageOgType }}">
    <meta property="og:site_name" content="{{ site_setting('identity.name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', str_replace('_', '-', app()->getLocale())) }}_TR">
    @if ($pageOgImage)
        <meta property="og:image" content="{{ $pageOgImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="{{ $pageOgImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    @if ($pageOgImage)
        <meta name="twitter:image" content="{{ $pageOgImage }}">
    @endif

    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    {{-- RSS alternate — only rendered when feed actually exists --}}
    @if (site_setting('features.feed_enabled'))
        <link rel="alternate" type="application/rss+xml" href="/feed.xml" title="{{ site_setting('identity.name') }} — Dispatches">
    @endif

    {{-- Pre-hydration theme — admin sets the default, user override via localStorage. light/dark only. --}}
    @php
        $themeDefault = site_setting('theme.dark_mode', 'light');
        if (! in_array($themeDefault, ['light', 'dark'], true)) $themeDefault = 'light';
    @endphp
    <script>
        (function () {
            try {
                var adminDefault = @json($themeDefault);
                var stored = localStorage.getItem('_x_theme-pref');
                var pref = stored ? JSON.parse(stored) : adminDefault;
                if (pref === 'system') {
                    pref = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    localStorage.setItem('_x_theme-pref', JSON.stringify(pref));
                }
                if (pref !== 'light' && pref !== 'dark') pref = 'light';
                document.documentElement.dataset.theme = pref;
            } catch (e) {}
        })();
    </script>

    {{-- Critical font preload --}}
    @php
        $sourceSerifUrl = \App\Support\Fonts::url('source-serif-4-latin-wght-normal');
        $plexSansUrl    = \App\Support\Fonts::url('ibm-plex-sans-latin-wght-normal');
    @endphp
    @if ($sourceSerifUrl)
        <link rel="preload" as="font" type="font/woff2" href="{{ $sourceSerifUrl }}" crossorigin>
    @endif
    @if ($plexSansUrl)
        <link rel="preload" as="font" type="font/woff2" href="{{ $plexSansUrl }}" crossorigin>
    @endif

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
