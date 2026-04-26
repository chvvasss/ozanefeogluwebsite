@extends('layouts.public', [
    'title' => $photo->getTranslationWithFallback('title').' · Görüntü · '.site_setting('identity.name'),
    'description' => $photo->getTranslationWithFallback('caption') ?: $photo->getTranslationWithFallback('title'),
])

@section('content')
<article class="photo-article">
    <section class="scene scene--darker">
        <div class="page-wrap">
            <figure class="photo-article-figure">
                <img src="{{ $photo->imageUrl('w1920') ?? $photo->imageUrl() }}"
                     srcset="{{ $photo->imageSrcset() }}"
                     sizes="(min-width: 1024px) 86vw, 100vw"
                     alt="{{ $photo->resolvedAltText() }}"
                     loading="eager"
                     fetchpriority="high"
                     width="1920" height="1280">
            </figure>
        </div>
    </section>

    <section class="scene scene--tight">
        <div class="page-wrap">
            <div class="dossier-grid gap-y-10">
                <header class="dg-8">
                    <p class="kicker kicker--accent">
                        @if ($photo->location)
                            {{ strtoupper($photo->location) }}
                            <span class="dateline-separator">·</span>
                        @endif
                        <span>{{ $photo->kind_label }}</span>
                    </p>
                    <h1 class="display-editorial mt-3">{{ $photo->getTranslationWithFallback('title') }}</h1>

                    @php $caption = $photo->getTranslationWithFallback('caption'); @endphp
                    @if ($caption)
                        <div class="standfirst mt-5 max-w-[58ch]">{{ $caption }}</div>
                    @endif
                </header>

                <aside class="dg-4 photo-article-meta">
                    <dl class="text-sm">
                        <div>
                            <dt>Künye</dt>
                            <dd>{{ $photo->resolvedCredit() }}</dd>
                        </div>
                        @if ($photo->captured_at)
                            <div>
                                <dt>Çekim</dt>
                                <dd class="tabular-nums">{{ $photo->captured_at->translatedFormat('d F Y') }}</dd>
                            </div>
                        @endif
                        @if ($photo->source)
                            <div>
                                <dt>Kaynak</dt>
                                <dd>{{ $photo->source }}</dd>
                            </div>
                        @endif
                        @if ($photo->license)
                            <div>
                                <dt>Lisans</dt>
                                <dd>{{ $photo->license }}</dd>
                            </div>
                        @endif
                        @if ($photo->rights_notes)
                            <div>
                                <dt>Hak notu</dt>
                                <dd>{{ $photo->rights_notes }}</dd>
                            </div>
                        @endif
                        @if ($photo->writing)
                            <div>
                                <dt>İlgili yazı</dt>
                                <dd><a href="{{ $photo->writing->url() }}" class="underline underline-offset-4">{{ $photo->writing->title }}</a></dd>
                            </div>
                        @endif
                    </dl>
                </aside>
            </div>
        </div>
    </section>

    <section class="scene scene--closing">
        <div class="page-wrap">
            <nav class="photo-prev-next" aria-label="Fotoğraflar arası gezinme">
                <div>
                    @if ($prev)
                        <a href="{{ $prev->url() }}" class="photo-prev-next-link" rel="prev">
                            <span class="eyebrow">← Önceki</span>
                            <span>{{ $prev->getTranslationWithFallback('title') }}</span>
                        </a>
                    @endif
                </div>
                <div class="text-right">
                    @if ($next)
                        <a href="{{ $next->url() }}" class="photo-prev-next-link" rel="next">
                            <span class="eyebrow">Sonraki →</span>
                            <span>{{ $next->getTranslationWithFallback('title') }}</span>
                        </a>
                    @endif
                </div>
            </nav>

            <p class="text-center mt-10">
                <a href="{{ route('visuals.index') }}" class="btn btn--secondary">Tüm arşive dön</a>
            </p>
        </div>
    </section>
</article>
@endsection
