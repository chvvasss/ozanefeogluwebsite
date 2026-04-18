@extends('layouts.public', [
    'title' => ($page->meta_title ?: $page->title).' · Ozan Efeoğlu',
    'description' => $page->meta_description ?: $page->intro,
])

@section('content')

@php
    $credentials = $page->extra('credentials', []);
    $timeline = $page->extra('timeline', []);
    $awards = $page->extra('awards', []);
    $cvUrl = $page->extra('cv_url', '#');
@endphp

<article class="about-page">

    {{-- =========================== MASTHEAD =========================== --}}
    <header class="about-masthead">
        <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-12 md:pt-20 pb-10 md:pb-16">
            <p class="eyebrow reveal reveal-1 mb-6">Hakkında · kısa biyografi</p>

            <div class="grid gap-8 lg:grid-cols-[1.5fr_1fr] items-end">
                <h1 class="display-fraunces reveal reveal-2 leading-[0.98]"
                    style="font-size: clamp(3rem, 8vw, 6.5rem); letter-spacing: var(--tracking-tightest);">
                    <span class="block">Ozan</span>
                    <em class="italic text-[var(--color-accent)] block">Efeoğlu</em>
                </h1>

                @if ($page->intro)
                    <p class="text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)] max-w-[44ch] reveal reveal-3 mb-2">
                        {{ $page->intro }}
                    </p>
                @endif
            </div>
        </div>
    </header>

    {{-- =========================== BIO + CREDENTIALS =========================== --}}
    <section class="border-t border-[var(--color-rule)]">
        <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-16 md:py-24 grid gap-12 lg:grid-cols-[20rem_1fr]">

            {{-- Sticky credentials sidebar --}}
            <aside class="lg:sticky lg:top-28 lg:self-start">
                <p class="eyebrow mb-4">Künye</p>
                <dl class="font-mono text-xs space-y-2 tabular-nums border-t border-[var(--color-rule)] pt-4">
                    @foreach ($credentials as $row)
                        <div class="grid grid-cols-[6rem_1fr] gap-3 py-1">
                            <dt class="text-[var(--color-ink-muted)]">{{ $row['label'] }}</dt>
                            <dd class="text-[var(--color-ink)]">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ $cvUrl }}" class="btn btn--ghost btn--sm">CV (PDF) ↗</a>
                    <a href="{{ route('contact') }}" class="btn btn--ghost btn--sm">İletişim →</a>
                </div>
            </aside>

            {{-- Long-form bio --}}
            <div class="prose-article">
                {!! $page->body !!}
            </div>
        </div>
    </section>

    {{-- =========================== TIMELINE =========================== --}}
    @if (! empty($timeline))
        <section class="bg-[var(--color-bg-muted)] border-t border-[var(--color-rule)]">
            <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-20 md:py-28">
                <div class="flex items-baseline justify-between mb-12">
                    <div>
                        <p class="eyebrow mb-3">Kronoloji</p>
                        <h2 class="display-fraunces text-[clamp(2rem,4vw,3rem)] leading-[1.05]"
                            style="letter-spacing: var(--tracking-tighter);">
                            On yılın <em class="italic text-[var(--color-accent)]">kilometre taşları</em>
                        </h2>
                    </div>
                </div>

                <ol class="timeline-rail">
                    @foreach ($timeline as $i => $entry)
                        <li class="timeline-row">
                            <div class="timeline-year">{{ $entry['year'] }}</div>
                            <div class="timeline-mark" aria-hidden="true">
                                <span class="timeline-dot"></span>
                            </div>
                            <div class="timeline-text">{{ $entry['text'] }}</div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- =========================== AWARDS =========================== --}}
    @if (! empty($awards))
        <section class="border-t border-[var(--color-rule)]">
            <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-20 md:py-24 grid gap-10 md:grid-cols-[1fr_2fr] items-start">
                <div>
                    <p class="eyebrow mb-3">Ödüller &amp; anma</p>
                    <h2 class="display-fraunces text-[clamp(1.75rem,3vw,2.5rem)] leading-[1.05]">
                        Dergi ve jürilerin <em class="italic text-[var(--color-accent)]">notları</em>
                    </h2>
                </div>
                <ol class="divide-y divide-[var(--color-rule)]">
                    @foreach ($awards as $award)
                        <li class="py-4 grid grid-cols-[5rem_1fr] gap-6">
                            <span class="font-mono text-sm tabular-nums text-[var(--color-ink-subtle)]">{{ $award['year'] }}</span>
                            <span class="display-fraunces text-lg leading-snug">{{ $award['title'] }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- =========================== RECENT WRITINGS =========================== --}}
    @if ($recentWritings->isNotEmpty())
        <section class="bg-[var(--color-bg-muted)] border-t border-[var(--color-rule)]">
            <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-20 md:py-24">
                <div class="flex items-baseline justify-between mb-10">
                    <div>
                        <p class="eyebrow mb-3">Son üç yazı</p>
                        <h2 class="display-fraunces text-[clamp(1.75rem,3vw,2.5rem)] leading-[1.05]">
                            Şu sıralar <em class="italic text-[var(--color-accent)]">ne yazıyor</em>
                        </h2>
                    </div>
                    <a href="{{ route('writing.index') }}" class="hidden md:inline-flex items-center gap-2 text-sm no-underline border-b border-[var(--color-ink)] pb-1">
                        Tüm arşiv <span aria-hidden="true">↗</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($recentWritings as $writing)
                        @include('partials._writing-card', ['writing' => $writing, 'variant' => 'default'])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- =========================== CTA =========================== --}}
    <section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-24 md:py-32 border-t border-[var(--color-rule)]">
        <p class="display-fraunces text-[clamp(1.75rem,4vw,3rem)] leading-[1.1] max-w-[28ch]"
           style="letter-spacing: var(--tracking-tighter);">
            Bir iş, bir basın sorusu, bir ipucu — <em class="italic text-[var(--color-accent)]">yaz</em>.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact') }}" class="btn btn--accent btn--lg">İletişim kanalları <span aria-hidden="true">→</span></a>
            <a href="{{ route('writing.index') }}" class="btn btn--ghost btn--lg">Yazıları oku</a>
        </div>
    </section>

</article>

@endsection
