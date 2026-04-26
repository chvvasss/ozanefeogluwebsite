@extends('layouts.public', [
    'title' => 'Görüntü · '.site_setting('identity.name'),
    'description' => 'Ozan Efeoğlu fotoğraf arşivi — saha, drone, portre, protokol ve editoryal seçki.',
])

@section('content')
<section class="scene scene--tight">
    <div class="page-wrap">
        <header class="visuals-hero">
            <p class="eyebrow">Arşiv · fotoğraf</p>
            <h1 class="display-editorial">Görüntü</h1>
            <p class="standfirst max-w-[58ch]">
                Saha, drone, protokol ve editoryal seçki. Tüm fotoğraflar künye ile — kaynak ve lisans kayıtlı.
            </p>
            <p class="dateline mt-5 tabular-nums">
                {{ $totalCount }} kayıt
                <span class="dateline-separator">·</span>
                tür: {{ $kind ? ($kindLabels[$kind] ?? $kind) : 'tümü' }}
            </p>

            <nav class="visuals-filter" aria-label="Tür filtresi">
                <a href="{{ route('visuals.index') }}"
                   aria-current="{{ $kind === null ? 'page' : 'false' }}"
                   class="visuals-filter-item">
                    tümü <span class="visuals-filter-count">{{ $totalCount }}</span>
                </a>
                @foreach ($kinds as $k)
                    @php $c = (int) ($counts[$k] ?? 0); @endphp
                    @if ($c === 0 && $kind !== $k)
                        @continue
                    @endif
                    <a href="{{ route('visuals.index', ['kind' => $k]) }}"
                       aria-current="{{ $kind === $k ? 'page' : 'false' }}"
                       class="visuals-filter-item">
                        {{ $kindLabels[$k] ?? $k }}
                        <span class="visuals-filter-count">{{ $c }}</span>
                    </a>
                @endforeach
            </nav>
        </header>
    </div>
</section>

<section class="scene scene--muted">
    <div class="page-wrap">
        @if ($photos->isEmpty())
            <div class="visuals-empty">
                <p class="eyebrow mb-2">Arşiv boş</p>
                <p class="text-[var(--color-ink-muted)] max-w-[48ch]">
                    Bu filtrede yayımlanmış fotoğraf yok. Tür filtresini değiştir ya da tümünü göster.
                </p>
            </div>
        @else
            <div class="visuals-grid">
                @foreach ($photos as $photo)
                    <a href="{{ $photo->url() }}" class="visuals-tile no-underline" aria-label="{{ $photo->getTranslationWithFallback('title') }}">
                        <figure class="visuals-tile-figure">
                            <img src="{{ $photo->imageUrl('w640') ?? $photo->imageUrl() }}"
                                 srcset="{{ $photo->imageSrcset() }}"
                                 sizes="(min-width: 1024px) 28vw, (min-width: 640px) 45vw, 95vw"
                                 alt="{{ $photo->resolvedAltText() }}"
                                 loading="lazy"
                                 width="1280" height="853">
                        </figure>
                        <figcaption class="visuals-tile-caption">
                            <span class="visuals-tile-title">{{ $photo->getTranslationWithFallback('title') }}</span>
                            <span class="visuals-tile-meta">
                                @if ($photo->location)
                                    <span>{{ strtoupper($photo->location) }}</span>
                                    <span class="dateline-separator">·</span>
                                @endif
                                <span>{{ $photo->kind_label }}</span>
                                @if ($photo->captured_at)
                                    <span class="dateline-separator">·</span>
                                    <span class="tabular-nums">{{ $photo->captured_at->format('Y-m') }}</span>
                                @endif
                            </span>
                        </figcaption>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $photos->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
