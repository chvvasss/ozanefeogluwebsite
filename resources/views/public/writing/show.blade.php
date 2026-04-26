@extends('layouts.public', ['title' => $writing->title.' · '.site_setting('identity.name')])

@section('content')

@php
    /** @var \App\Models\Writing $writing */
    $dateLong  = optional($writing->published_at)->translatedFormat('d F Y');
    $dateShort = optional($writing->published_at)->format('Y-m');
    $kindLabel = $writing->kind_label;
    $hasCover  = method_exists($writing, 'hasCover') && $writing->hasCover();
@endphp

<article class="dispatch-page">

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 1 — HEADLINE OVERTURE
         Kicker (crimson, series label) + statuesque headline + italic
         standfirst + dateline strip. No cover here; cover lives in Scene 2
         so the opening breath is typography-only.
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--overture">
        <div class="page-wrap">
            <nav class="mb-12" aria-label="Gezinti">
                <a href="{{ route('writing.index') }}" class="link-quiet text-sm">
                    <span aria-hidden="true">←</span> Tüm yazılar
                </a>
            </nav>

            <div class="dispatch-overture">
                <p class="kicker kicker--accent">
                    {{ strtoupper($kindLabel) }}
                    @if ($writing->location)
                        <span class="dateline-separator">·</span>
                        <span class="text-[var(--color-ink)]">{{ strtoupper($writing->location) }}</span>
                    @endif
                </p>

                <h1 class="display-statuesque mt-5" style="font-size: clamp(var(--text-5xl), 8vw, var(--text-8xl));">
                    {{ $writing->title }}
                </h1>

                @if ($writing->excerpt)
                    <p class="standfirst mt-8" style="font-size: clamp(var(--text-lg), 2.4vw, var(--text-2xl)); max-width: 52ch;">
                        {{ $writing->excerpt }}
                    </p>
                @endif

                <p class="dispatch-dateline mt-10">
                    <span>{{ $dateLong }}</span>
                    <span class="dateline-separator">·</span>
                    <span class="tabular-nums">{{ $writing->read_minutes }} dakikalık okuma</span>
                    @if ($writing->publications->isNotEmpty())
                        <span class="dateline-separator">·</span>
                        <span>Yayın:
                            @foreach ($writing->publications as $pub)
                                @if ($pub->pivot->link)
                                    <a href="{{ $pub->pivot->link }}" target="_blank" rel="noopener" class="link-quiet">{{ $pub->name }} ↗</a>
                                @else
                                    {{ $pub->name }}
                                @endif
                                @if (! $loop->last),@endif
                            @endforeach
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 2 — COVER (only if real photo exists; no skeleton ever)
         Full-bleed dark strip lifts the image out of the page rhythm.
         Caption is a three-register composition: LOC (bold caps) +
         descriptive sentence + photographer/agency credit (italic).
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($hasCover)
        <section class="scene scene--inverse scene--tight full-bleed-strip">
            <div class="page-wrap">
                <figure class="dispatch-cover-hero">
                    <img src="{{ $writing->coverUrl('w1920') ?? $writing->coverUrl() }}"
                         srcset="{{ $writing->coverSrcset() }}"
                         sizes="(min-width: 1320px) 1320px, 100vw"
                         alt="{{ $writing->title }}"
                         width="1920" height="1080">
                    <figcaption>
                        @if ($writing->location)
                            <span class="photo-caption-loc">{{ strtoupper($writing->location) }}</span>
                        @endif
                        @if ($dateShort) · {{ $dateShort }}@endif
                        · {{ $kindLabel }}
                        <span class="photo-caption-credit">· {{ $writing->resolvedPhotoCredit() }}</span>
                    </figcaption>
                </figure>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 3 — BODY + MARGINALIA
         Left rail: tabular meta (year-marks-adjacent, not full dossier dl).
         Center: prose-article. Right: breath column (empty on purpose).
         Body starts with a mono-caps lede opener: "KONUM —" then prose.
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene {{ $hasCover ? '' : 'scene--tight' }}">
        <div class="page-wrap">
            <div class="dossier-grid">
                <aside class="hidden lg:block dg-2">
                    <dl class="marginalia-rail sticky top-32">
                        <div>
                            <dt>Yayım</dt>
                            <dd>{{ optional($writing->published_at)->format('Y-m-d') }}</dd>
                        </div>
                        @if ($writing->location)
                            <div>
                                <dt>Konum</dt>
                                <dd>{{ $writing->location }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt>Tür</dt>
                            <dd>{{ $kindLabel }}</dd>
                        </div>
                        <div>
                            <dt>Okuma</dt>
                            <dd>{{ $writing->read_minutes }} dk</dd>
                        </div>
                        @if ($writing->publications->isNotEmpty())
                            <div>
                                <dt>Yayın</dt>
                                @foreach ($writing->publications as $pub)
                                    <dd>
                                        @if ($pub->pivot->link)
                                            <a href="{{ $pub->pivot->link }}" target="_blank" rel="noopener" class="link-quiet">{{ $pub->name }} ↗</a>
                                        @else
                                            {{ $pub->name }}
                                        @endif
                                    </dd>
                                @endforeach
                            </div>
                        @endif
                    </dl>
                </aside>

                <div class="dg-8 dg-start-3">
                    @if ($writing->location)
                        <p class="dispatch-lede-open">{{ strtoupper($writing->location) }} —</p>
                    @endif
                    <div class="prose-article dispatch-prose">
                        {!! $writing->body !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 4 — BYLINE COLOPHON
         Dark scene, author plate. Identity + contact line. A proper close.
         ════════════════════════════════════════════════════════════════════ --}}
    <section class="scene scene--inverse scene--tight">
        <div class="page-wrap">
            <div class="dossier-grid items-baseline gap-y-6">
                <div class="dg-7">
                    <p class="eyebrow mb-3">Yazan</p>
                    <p class="display-quiet" style="color: var(--layer-ink-inverse);">
                        {{ site_setting('identity.name') }}
                    </p>
                    <p class="mt-3 subhead-pipe" style="color: color-mix(in oklch, var(--layer-ink-inverse) 70%, transparent);">
                        <span>foto muhabir</span><span>yayıncı</span><span>araştırmacı</span>
                    </p>
                    <p class="mt-4 text-sm leading-[1.6] max-w-[58ch]" style="color: color-mix(in oklch, var(--layer-ink-inverse) 80%, transparent);">
                        {{ site_setting('identity.description') }}
                    </p>
                </div>
                <div class="dg-5 md:text-right">
                    @if (site_setting('contact.email'))
                        <p>
                            <a href="mailto:{{ site_setting('contact.email') }}?subject={{ urlencode('Dispatch: '.$writing->title) }}"
                               class="dispatch-byline-mail">
                                {{ site_setting('contact.email') }}
                            </a>
                        </p>
                    @endif
                    <p class="mt-3">
                        <a href="{{ route('contact') }}" class="link-quiet text-sm">
                            Tüm iletişim kanalları <span aria-hidden="true">→</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 5 — PREV / NEXT (asymmetric type-led transition)
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($prev || $next)
        <section class="scene scene--tight">
            <div class="page-wrap">
                <nav class="dossier-grid gap-y-10 items-baseline" aria-label="Yazı gezintisi">
                    <div class="dg-6">
                        @if ($prev)
                            <a href="{{ $prev->url() }}" class="dispatch-nav-link block no-underline group">
                                <span class="dateline"><span aria-hidden="true">←</span> Önceki yazı</span>
                                <span class="dispatch-nav-title block mt-3">{{ $prev->title }}</span>
                                @if ($prev->location)
                                    <span class="dateline mt-2 block">{{ strtoupper($prev->location) }}
                                        @if (optional($prev->published_at))
                                            <span class="dateline-separator">·</span>
                                            {{ optional($prev->published_at)->format('d.m.Y') }}
                                        @endif
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>
                    <div class="dg-6 md:text-right">
                        @if ($next)
                            <a href="{{ $next->url() }}" class="dispatch-nav-link block no-underline group">
                                <span class="dateline">Sonraki yazı <span aria-hidden="true">→</span></span>
                                <span class="dispatch-nav-title block mt-3">{{ $next->title }}</span>
                                @if ($next->location)
                                    <span class="dateline mt-2 block">{{ strtoupper($next->location) }}
                                        @if (optional($next->published_at))
                                            <span class="dateline-separator">·</span>
                                            {{ optional($next->published_at)->format('d.m.Y') }}
                                        @endif
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>
                </nav>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         SCENE 6 — RELATED (writing-row text list)
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($related->isNotEmpty())
        <section class="scene scene--muted scene--closing border-t border-[var(--color-rule)]">
            <div class="page-wrap">
                <header class="flex items-baseline justify-between gap-6 mb-8">
                    <div>
                        <p class="eyebrow mb-2">Aynı türden</p>
                        <h2 class="display-quiet">Daha fazla {{ $kindLabel }}</h2>
                    </div>
                    <a href="{{ route('writing.index') }}" class="link-quiet text-sm">
                        Tüm arşiv <span aria-hidden="true">→</span>
                    </a>
                </header>
                <div class="border-t border-[var(--color-ink)]">
                    @foreach ($related as $item)
                        @include('partials._writing-row', ['writing' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</article>

@endsection
