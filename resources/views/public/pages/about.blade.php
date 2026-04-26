@php
    $rawTitle = $page->meta_title ?: $page->title;
    $siteName = site_setting('identity.name');
    $finalTitle = str_contains((string) $rawTitle, (string) $siteName) ? $rawTitle : $rawTitle.' · '.$siteName;
@endphp
@extends('layouts.public', [
    'title' => $finalTitle,
    'description' => $page->meta_description ?: $page->intro,
])

@section('content')

@php
    /** @var \App\Models\Page $page */
    /** @var \Illuminate\Support\Collection $recentWritings */

    $identities  = $page->extra('identities', []);
    $affiliation = $page->extra('affiliation');
    $workareas   = $page->extra('workareas', []);
    $timeline    = $page->extra('timeline', []);
    $methodology = $page->extra('methodology');
    $research    = $page->extra('research');

    $portraitUrl    = site_setting('identity.portrait_url');
    $portraitCredit = site_setting('identity.portrait_credit');
@endphp

<article>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 1 — PORTRAIT OVERTURE
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--overture">
        <div class="page-wrap">
            <div class="dossier-grid items-center gap-y-12">

                <div class="{{ $portraitUrl ? 'dg-5' : 'dg-12 max-w-[68ch]' }}">
                    <div class="editorial-plate">
                        <p class="eyebrow">Hakkında</p>

                        <h1 class="display-statuesque">{{ site_setting('identity.name') }}</h1>

                        @if (! empty($identities))
                            <ul class="plate-roles" aria-label="Kimlik">
                                @foreach ($identities as $i => $identity)
                                    @php
                                        $cls = $i === 0 ? 'plate-role--primary'
                                             : ($i === 1 ? 'plate-role--secondary' : 'plate-role--tertiary');
                                    @endphp
                                    <li class="{{ $cls }}">{{ $identity }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($affiliation && site_setting('identity.affiliation_approved'))
                            <p class="plate-affiliation">{{ $affiliation }}</p>
                        @endif

                        @if ($page->intro)
                            <p class="standfirst mt-2">{{ $page->intro }}</p>
                        @endif
                    </div>
                </div>

                @if ($portraitUrl)
                    <figure class="dg-7 m-0">
                        <img class="overture-portrait"
                             src="{{ $portraitUrl }}"
                             alt="{{ site_setting('identity.name') }} — portre"
                             width="900" height="1200"
                             loading="eager"
                             fetchpriority="high">
                        @if ($portraitCredit)
                            <figcaption class="portrait-caption">{{ $portraitCredit }}</figcaption>
                        @endif
                    </figure>
                @endif
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 2 — THE ESSAY
         Sticky year-marks (left rail) + running prose body (right).
         Chronology is woven into the reading, not a separate "timeline file".
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--muted">
        <div class="page-wrap">
            <div class="dossier-grid gap-y-10">

                <aside class="dg-3 lg:sticky lg:top-32 lg:self-start">
                    <p class="eyebrow mb-5">Yol</p>
                    @if (! empty($timeline))
                        <ol class="year-marks">
                            @foreach ($timeline as $entry)
                                <li>
                                    <time datetime="{{ $entry['year'] }}">{{ $entry['year'] }}</time>
                                    <span>{{ $entry['text'] }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </aside>

                <div class="dg-9 prose-article">
                    {!! $page->body !!}
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 3 — ATÖLYE
         Work areas as paragraphs (not stat grid). Alternating 3/5 columns;
         nth-child(even) flips meta to the right (CSS in _components.css).
         ════════════════════════════════════════════════════════════════════ --}}
    @if (! empty($workareas))
        <section class="scene">
            <div class="page-wrap">
                <header class="flex items-baseline justify-between gap-6 mb-10">
                    <div>
                        <p class="eyebrow mb-2">Nerede ne yapar</p>
                        <h2 class="display-editorial">Atölye</h2>
                    </div>
                </header>

                <ol class="atolye-list">
                    @foreach ($workareas as $area)
                        <li class="atolye-scene">
                            <div class="atolye-scene-inner">
                                <div class="atolye-scene-meta">
                                    <p class="atolye-scene-label">{{ $area['label'] ?? '' }}</p>
                                    <h3 class="atolye-scene-title">{{ $area['title'] ?? '' }}</h3>
                                </div>
                                <div class="atolye-scene-body">
                                    @if (! empty($area['lines']))
                                        {{ implode(' · ', $area['lines']) }}
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 4 — RESEARCH POSTER
         Single-cell composition. Italic display title + mono cite footer.
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($research)
        <section class="scene scene--darker">
            <div class="page-wrap">
                <div class="research-poster">
                    <p class="research-poster-kicker">
                        Akademi
                        <span class="dateline-separator">·</span>
                        Yüksek lisans araştırması
                    </p>
                    @if (! empty($research['title']))
                        <p class="research-poster-question">{{ $research['title'] }}</p>
                    @endif
                    @if (! empty($research['note']))
                        <p class="research-poster-note">{{ $research['note'] }}</p>
                    @endif
                    @if (! empty($research['place']))
                        <p class="research-poster-cite">{{ $research['place'] }}</p>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 5 — METHODOLOGY (optional inline scene)
         Short paragraph, quiet rhythm. Only renders if set in seeder.
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($methodology)
        <section class="scene scene--tight">
            <div class="page-wrap">
                <div class="dossier-grid gap-y-6">
                    <div class="dg-3">
                        <p class="eyebrow">Metot</p>
                    </div>
                    <p class="dg-9 text-[var(--text-md)] leading-[1.7] text-[var(--color-ink)] max-w-[62ch]">
                        {{ $methodology }}
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 6 — CLOSING (recent dispatches + contact invitation)
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($recentWritings->isNotEmpty())
        <section class="scene scene--closing border-t border-[var(--color-rule)]">
            <div class="page-wrap">
                <header class="flex items-baseline justify-between gap-6 mb-8">
                    <div>
                        <p class="eyebrow mb-2">Son dispatches</p>
                        <h2 class="display-quiet">Şu sıralar yazdıkları</h2>
                    </div>
                    <a href="{{ route('writing.index') }}" class="link-quiet text-sm">
                        Tüm arşiv <span aria-hidden="true">→</span>
                    </a>
                </header>
                <div class="border-t border-[var(--color-ink)]">
                    @foreach ($recentWritings as $writing)
                        @include('partials._writing-row', ['writing' => $writing])
                    @endforeach
                </div>

                <p class="mt-10 text-sm">
                    <a href="{{ route('contact') }}" class="link-quiet">
                        İletişim kanalları <span aria-hidden="true">→</span>
                    </a>
                </p>
            </div>
        </section>
    @endif

</article>

@endsection
