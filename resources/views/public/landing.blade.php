@extends('layouts.public')

@section('content')

{{-- ================================================== HERO ================================================== --}}

<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-16 md:pt-24 pb-20 md:pb-28">
    <p class="eyebrow reveal reveal-1 mb-7">
        Saha yazıları · röportaj · deneme
    </p>

    <h1 class="hero-display reveal reveal-2 max-w-[14ch]">
        Gittim,<br>
        baktım,<br>
        <em>yazdım</em>.
    </h1>

    <div class="mt-10 md:mt-14 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-8 md:gap-12 items-end reveal reveal-3">
        <p class="max-w-[56ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
            Ortadoğu, Kafkasya ve Doğu Avrupa'dan saha röportajları; aradaki notlar; uzun denemeler.
            On yıllık bir defterden Türkçe açık yayın. İstanbul tabanlı, bağımsız, akreditasyonlu.
        </p>

        <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
            <a href="{{ route('writing.index') }}" class="btn btn--accent">
                Yazıları oku
                <span aria-hidden="true">→</span>
            </a>
            <a href="{{ route('about') }}" class="btn btn--ghost">
                Hakkımda
            </a>
        </div>
    </div>
</section>

{{-- ================================================== BYLINES ================================================== --}}

<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-8 md:py-10 border-y border-[var(--color-rule)]">
    <ul class="flex flex-wrap items-center justify-center md:justify-start gap-x-8 gap-y-3 font-mono text-[0.7rem] uppercase tracking-[0.18em] text-[var(--color-ink-muted)]">
        <li class="text-[var(--color-ink-subtle)] mr-2">yazdığı yayınlar &mdash;</li>
        @foreach ($credits as $credit)
            <li>{{ $credit }}</li>
        @endforeach
    </ul>
</section>

{{-- ================================================== WRITING GRID ================================================== --}}

<section id="yazilar" class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-20 md:pt-28 pb-20 md:pb-24">
    <div class="flex items-end justify-between mb-12 md:mb-16">
        <div>
            <p class="eyebrow mb-3">Son yazılar</p>
            <h2 class="display-fraunces text-[clamp(2.5rem,5vw,3.5rem)] leading-[1.02]" style="letter-spacing: var(--tracking-tighter);">
                Arşivden <em class="italic text-[var(--color-accent)]">seçme</em>
            </h2>
        </div>
        <a href="{{ route('writing.index') }}" class="hidden md:inline-flex items-center gap-2 text-sm no-underline pb-1 text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">
            Tümü
            <span aria-hidden="true">→</span>
        </a>
    </div>

    @php
        $collection = collect($writings);
        $hero = $collection->firstWhere('is_featured', true) ?? $collection->first();
        $rest = $hero ? $collection->reject(fn ($w) => $w->id === $hero->id)->values() : $collection;
    @endphp

    @if ($hero)
        <div class="mb-14 md:mb-20">
            @include('partials._writing-card', ['writing' => $hero, 'variant' => 'hero'])
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
        @foreach ($rest as $entry)
            @include('partials._writing-card', ['writing' => $entry, 'variant' => 'default'])
        @endforeach
    </div>

    <div class="md:hidden mt-10 flex justify-center">
        <a href="{{ route('writing.index') }}" class="btn btn--ghost">Tümünü gör →</a>
    </div>
</section>

{{-- ================================================== FOOTER CTA ================================================== --}}

<section class="border-t border-[var(--color-rule)]">
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-20 md:py-28 grid gap-10 md:grid-cols-2 items-end">
        <div>
            <p class="eyebrow mb-3">Daha fazlası</p>
            <h2 class="display-fraunces text-[clamp(2rem,4vw,3rem)] leading-[1.05]" style="letter-spacing: var(--tracking-tighter);">
                Uzun özgeçmiş,<br>
                güvenli <em class="italic text-[var(--color-accent)]">kanallar</em>.
            </h2>
        </div>
        <div class="flex flex-wrap gap-3 md:justify-end">
            <a href="{{ route('about') }}" class="btn btn--ghost btn--lg">
                Hakkımda
                <span aria-hidden="true" class="opacity-60">→</span>
            </a>
            <a href="{{ route('contact') }}" class="btn btn--accent btn--lg">
                İletişim
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

@endsection
