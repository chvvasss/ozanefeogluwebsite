@extends('layouts.public')

@section('content')

@php
    /** @var string $heroMode — featured_photo|rotation|typographic|portrait (resolved by HomeController) */
    /** @var \App\Models\Writing|null $heroItem */
    /** @var string|null $heroEyebrow */
    /** @var string $ctaPrimaryLabel */
    /** @var string $ctaPrimaryUrl */
    /** @var string $ctaSecondaryLabel */
    /** @var string $ctaSecondaryUrl */
    /** @var \App\Models\Writing|null $leadItem */
    /** @var \Illuminate\Support\Collection $constellationItems */
    /** @var \Illuminate\Support\Collection $recent */
    /** @var array $workareas */
    /** @var string|null $intro */
    /** @var string|null $manifestoQuote */
    /** @var array<int, string> $credits */
    /** @var string|null $portraitUrl */
    /** @var string|null $portraitCredit */

    $leadHasPhoto = $leadItem
        && method_exists($leadItem, 'hasCover')
        && $leadItem->hasCover();

    $heroData = [
        'intro'          => $intro,
        'eyebrow'        => $heroEyebrow,
        'primaryLabel'   => $ctaPrimaryLabel,
        'primaryUrl'     => $ctaPrimaryUrl,
        'secondaryLabel' => $ctaSecondaryLabel,
        'secondaryUrl'   => $ctaSecondaryUrl,
    ];
@endphp

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 1 — HERO (mode-aware; controlled by admin → Ayarlar → Hero)
     Variants: featured_photo · rotation · typographic · portrait
     featured_photo / rotation share the same template (photo hero).
     ════════════════════════════════════════════════════════════════════════ --}}
@switch($heroMode)
    @case('featured_photo')
    @case('rotation')
        @include('public.partials.hero.featured_photo', array_merge($heroData, ['heroItem' => $heroItem]))
        @break

    @case('portrait')
        @include('public.partials.hero.portrait', array_merge($heroData, [
            'portraitUrl'    => $portraitUrl,
            'portraitCredit' => $portraitCredit,
        ]))
        @break

    @default
        @include('public.partials.hero.typographic', $heroData)
@endswitch

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 2 — FEATURED DOSSIER
     Image-led if photo; typography-dominant if not (composition inversion,
     no empty blocks, no "coming soon", no fake image).
     ════════════════════════════════════════════════════════════════════════ --}}
@if ($leadItem)
    <section class="scene scene--muted">
        <div class="page-wrap">
            @if ($leadHasPhoto)
                <div class="dossier-grid gap-y-8 items-start">
                    <a href="{{ $leadItem->url() }}" class="dg-7 block no-underline" aria-label="{{ $leadItem->title }}">
                        <figure class="dispatch-cover dispatch-cover--landscape" style="border: 0;">
                            <img src="{{ $leadItem->coverUrl('w1920') ?? $leadItem->coverUrl() }}"
                                 srcset="{{ $leadItem->coverSrcset() }}"
                                 sizes="(min-width: 1024px) 60vw, 100vw"
                                 alt="{{ $leadItem->title }}"
                                 width="1280" height="853">
                        </figure>
                    </a>
                    <div class="dg-5">
                        <p class="kicker kicker--accent">
                            Son dosya
                            @if ($leadItem->location)
                                <span class="dateline-separator">·</span>
                                <span class="text-[var(--color-ink-muted)]">{{ strtoupper($leadItem->location) }}</span>
                            @endif
                        </p>
                        <a href="{{ $leadItem->url() }}" class="no-underline block mt-4">
                            <h2 class="display-editorial">{{ $leadItem->title }}</h2>
                        </a>
                        @if ($leadItem->excerpt)
                            <p class="standfirst mt-5">{{ $leadItem->excerpt }}</p>
                        @endif
                        <p class="dateline mt-6 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                            <span>{{ optional($leadItem->published_at)->translatedFormat('d F Y') }}</span>
                            <span class="dateline-separator">·</span>
                            <span>{{ $leadItem->kind_label }}</span>
                            <span class="dateline-separator">·</span>
                            <span class="tabular-nums">{{ $leadItem->read_minutes }} dk</span>
                        </p>
                        <p class="mt-6">
                            <a href="{{ $leadItem->url() }}" class="link-quiet text-sm">
                                Devamını oku <span aria-hidden="true">→</span>
                            </a>
                        </p>
                    </div>
                </div>
            @else
                {{-- No photo: typographic cover + companion text --}}
                <div class="dossier-grid gap-y-8 items-start">
                    <a href="{{ $leadItem->url() }}" class="dg-7 typo-cover-link no-underline"
                       aria-label="{{ $leadItem->title }}">
                        <div class="typo-cover typo-cover--featured typo-cover--{{ $leadItem->kind }}">
                            <p class="typo-cover-kicker">
                                {{ strtoupper($leadItem->kind_label) }}
                                @if ($leadItem->location)
                                    <span>·</span>
                                    {{ strtoupper($leadItem->location) }}
                                @endif
                            </p>
                            <h2 class="typo-cover-title">{{ $leadItem->title }}</h2>
                            <p class="typo-cover-mark">
                                {{ optional($leadItem->published_at)->format('Y-m-d') }}
                                <span>·</span>
                                OE
                            </p>
                        </div>
                    </a>
                    <div class="dg-5">
                        <p class="kicker kicker--accent">
                            Son dosya
                            @if ($leadItem->location)
                                <span class="dateline-separator">·</span>
                                <span class="text-[var(--color-ink-muted)]">{{ strtoupper($leadItem->location) }}</span>
                            @endif
                        </p>
                        <a href="{{ $leadItem->url() }}" class="no-underline block mt-4">
                            <h3 class="display-editorial">{{ $leadItem->title }}</h3>
                        </a>
                        @if ($leadItem->excerpt)
                            <p class="standfirst mt-5">{{ $leadItem->excerpt }}</p>
                        @endif
                        <p class="dateline mt-6 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                            <span>{{ optional($leadItem->published_at)->translatedFormat('d F Y') }}</span>
                            <span class="dateline-separator">·</span>
                            <span>{{ $leadItem->kind_label }}</span>
                            <span class="dateline-separator">·</span>
                            <span class="tabular-nums">{{ $leadItem->read_minutes }} dk</span>
                        </p>
                        <p class="mt-6">
                            <a href="{{ $leadItem->url() }}" class="link-quiet text-sm">
                                Devamını oku <span aria-hidden="true">→</span>
                            </a>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 3 — CONSTELLATIONS (mason 2x2 selected works)
     Row heights content-led (mason). Each card: thumb-led OR title-dominant
     fallback. No small "kind code" boxes — typography carries.
     ════════════════════════════════════════════════════════════════════════ --}}
@if ($constellationItems->isNotEmpty())
    <section class="scene scene--tight">
        <div class="page-wrap">
            <header class="flex items-baseline justify-between gap-6 mb-10">
                <div>
                    <p class="eyebrow mb-2">Seçki</p>
                    <h2 class="display-editorial">Seçilmiş çalışmalar</h2>
                </div>
                <a href="{{ route('writing.index') }}" class="hidden md:inline-flex link-quiet text-sm">
                    Tüm arşiv <span aria-hidden="true">→</span>
                </a>
            </header>

            <div class="mason-2x2">
                @foreach ($constellationItems as $item)
                    @php $hasThumb = method_exists($item, 'hasCover') && $item->hasCover(); @endphp
                    <a href="{{ $item->url() }}"
                       class="constellation-card typo-cover-link"
                       aria-label="{{ $item->title }}">

                        @if ($hasThumb)
                            <figure class="constellation-card-thumb">
                                <img src="{{ $item->coverUrl('w1280') ?? $item->coverUrl() }}"
                                     srcset="{{ $item->coverSrcset() }}"
                                     sizes="(min-width: 1024px) 40vw, 100vw"
                                     alt="{{ $item->title }}"
                                     width="800" height="1000"
                                     loading="lazy">
                            </figure>
                            <p class="constellation-card-kicker">
                                {{ $item->kind_label }}
                                @if ($item->location)
                                    <span class="dateline-separator">·</span>
                                    {{ strtoupper($item->location) }}
                                @endif
                            </p>
                            <h3 class="constellation-card-title">{{ $item->title }}</h3>
                            @if ($item->excerpt)
                                <p class="constellation-card-lede">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt), 130) }}
                                </p>
                            @endif
                            <p class="constellation-card-meta tabular-nums">
                                {{ optional($item->published_at)->format('d.m.Y') }}
                                <span class="dateline-separator">·</span>
                                {{ $item->read_minutes }} dk
                            </p>
                        @else
                            {{-- Typo-cover zaten kicker + title + date taşıyor;
                                 altta yalnız lede + okuma süresi gösterilir (duplicate yok). --}}
                            <div class="typo-cover typo-cover--{{ $item->kind }}">
                                <p class="typo-cover-kicker">
                                    {{ strtoupper($item->kind_label) }}
                                    @if ($item->location)
                                        <span>·</span>
                                        {{ strtoupper($item->location) }}
                                    @endif
                                </p>
                                <h3 class="typo-cover-title">{{ $item->title }}</h3>
                                <p class="typo-cover-mark">
                                    {{ optional($item->published_at)->format('Y-m-d') }}
                                    <span>·</span>
                                    {{ $item->read_minutes }} DK
                                </p>
                            </div>
                            @if ($item->excerpt)
                                <p class="constellation-card-lede mt-4">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt), 130) }}
                                </p>
                            @endif
                        @endif
                    </a>
                @endforeach
            </div>

            <p class="md:hidden mt-10">
                <a href="{{ route('writing.index') }}" class="link-quiet text-sm">
                    Tüm arşiv <span aria-hidden="true">→</span>
                </a>
            </p>
        </div>
    </section>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 3.5 — CONTACT SHEET
     Horizontal strip of featured archive photos; clicking jumps to /goruntu.
     Frame-by-frame feel — reminiscent of a photographer's proof sheet.
     ════════════════════════════════════════════════════════════════════════ --}}
@if (isset($photoStrip) && $photoStrip->isNotEmpty())
    <section class="scene scene--muted contact-sheet-scene">
        <div class="page-wrap">
            <header class="flex items-baseline justify-between gap-6 mb-8">
                <div>
                    <p class="eyebrow mb-2">Arşiv · kontrol baskısı</p>
                    <h2 class="display-editorial">Son kareler</h2>
                </div>
                <a href="{{ route('visuals.index') }}" class="hidden md:inline-flex link-quiet text-sm">
                    Tüm görüntü arşivi <span aria-hidden="true">→</span>
                </a>
            </header>

            <div class="contact-sheet" role="list">
                @foreach ($photoStrip as $index => $photo)
                    <a href="{{ $photo->url() }}"
                       class="contact-sheet-frame"
                       role="listitem"
                       aria-label="{{ $photo->getTranslationWithFallback('title') }}">
                        <figure class="contact-sheet-figure">
                            <img src="{{ $photo->imageUrl('w640') ?? $photo->imageUrl() }}"
                                 srcset="{{ $photo->imageSrcset() }}"
                                 sizes="(min-width: 1024px) 16vw, (min-width: 640px) 33vw, 50vw"
                                 alt="{{ $photo->resolvedAltText() }}"
                                 loading="lazy"
                                 width="640" height="427">
                        </figure>
                        <figcaption class="contact-sheet-caption">
                            <span class="contact-sheet-index tabular-nums">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            @if ($photo->location)
                                <span class="contact-sheet-loc">{{ strtoupper($photo->location) }}</span>
                            @endif
                        </figcaption>
                    </a>
                @endforeach
            </div>

            <p class="md:hidden mt-8 text-center">
                <a href="{{ route('visuals.index') }}" class="link-quiet text-sm">
                    Tüm görüntü arşivi <span aria-hidden="true">→</span>
                </a>
            </p>
        </div>
    </section>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 4 — PROFILE AS SCENE
     Left: pull-quote (manifesto, if set) OR editorial title (fallback).
     Right: atölye mini — 4 work-area compact strip.
     ════════════════════════════════════════════════════════════════════════ --}}
@if (! empty($workareas))
    <section class="scene scene--darker">
        <div class="page-wrap">
            <div class="dossier-grid gap-y-10">

                <div class="dg-5">
                    @if ($manifestoQuote)
                        <blockquote class="pull-quote">
                            {{ $manifestoQuote }}
                            <cite>{{ site_setting('identity.name') }}</cite>
                        </blockquote>
                    @else
                        <div>
                            <p class="eyebrow mb-3">Çalışma alanları</p>
                            <h2 class="display-editorial">
                                Nerede ne yapıyor
                            </h2>
                            <p class="mt-5 text-[var(--text-md)] leading-[1.65] text-[var(--color-ink-muted)] max-w-[40ch]">
                                Saha, görsel, araştırma ve yayıncılık — dördü birden aynı masada.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="dg-7">
                    <ul class="atolye-mini">
                        @foreach (array_slice($workareas, 0, 4) as $area)
                            <li>
                                <p class="atolye-mini-label">{{ $area['label'] ?? '' }}</p>
                                <div class="atolye-mini-content">
                                    <h3 class="atolye-mini-title">{{ $area['title'] ?? '' }}</h3>
                                    @if (! empty($area['lines']))
                                        <p class="atolye-mini-lines">
                                            {{ implode(' · ', array_slice($area['lines'], 0, 2)) }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-6">
                        <a href="{{ route('about') }}" class="link-quiet text-sm">
                            Tam biyografi <span aria-hidden="true">→</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 5 — RECENT DISPATCHES (rhythm break: text-row list)
     ════════════════════════════════════════════════════════════════════════ --}}
@if ($recent->isNotEmpty())
    <section class="scene scene--tight">
        <div class="page-wrap">
            <header class="flex items-baseline justify-between gap-6 mb-8">
                <h2 class="display-quiet">Son dispatch</h2>
                <a href="{{ route('writing.index') }}" class="link-quiet text-sm">
                    Tüm arşiv <span aria-hidden="true">→</span>
                </a>
            </header>
            <div class="border-t border-[var(--color-ink)]">
                @foreach ($recent as $entry)
                    @include('partials._writing-row', ['writing' => $entry])
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     SCENE 6 — BYLINES (closing rhythm)
     ════════════════════════════════════════════════════════════════════════ --}}
@if (! empty($credits))
    <section class="scene scene--closing border-t border-[var(--color-rule)]">
        <div class="page-wrap">
            <p class="eyebrow mb-5">Yayımlandığı yerler</p>
            <ul class="flex flex-wrap items-center gap-x-8 gap-y-2 font-mono text-[0.72rem] uppercase tracking-[var(--tracking-caps)] text-[var(--color-ink-muted)]">
                @foreach ($credits as $credit)
                    <li>{{ $credit }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

@endsection
