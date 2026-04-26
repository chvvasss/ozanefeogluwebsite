@props(['writing', 'variant' => 'default'])

@php
    /** @var \App\Models\Writing $writing */
    $href = $writing->url();
    $kindLabel = $writing->kind_label;
    $locationUpper = strtoupper((string) ($writing->location ?? ''));
    $dateShort = optional($writing->published_at)->format('Y-m');
    $dateLong = optional($writing->published_at)->translatedFormat('d F Y');
    $hasCover = method_exists($writing, 'hasCover') && $writing->hasCover();
@endphp

<a href="{{ $href }}"
   class="writing-card {{ $variant === 'hero' ? 'writing-card--hero block' : '' }}"
   aria-label="{{ $writing->title }}">
    @if ($hasCover)
        <figure class="writing-cover {{ $variant === 'hero' ? 'writing-cover--hero' : '' }}">
            <img src="{{ $writing->coverUrl('w1280') ?? $writing->coverUrl() }}"
                 srcset="{{ $writing->coverSrcset() }}"
                 sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                 alt="{{ $writing->title }}"
                 loading="lazy"
                 width="1280" height="{{ $variant === 'hero' ? 720 : 960 }}">
            <figcaption>{{ $locationUpper }} · {{ $dateShort }} · {{ $kindLabel }}</figcaption>
        </figure>
    @else
        <figure class="cover-skeleton {{ $variant === 'hero' ? 'cover-skeleton--hero' : '' }}">
            <span class="cover-skeleton-mark">Fotoğraf eklenmedi</span>
            <span class="cover-skeleton-meta">{{ $locationUpper }} · {{ $dateShort }} · {{ $kindLabel }}</span>
        </figure>
    @endif

    @if ($variant === 'hero')
        <div class="writing-card-body md:grid md:grid-cols-[2fr_1fr] md:gap-10 md:items-start md:pt-6">
            <div>
                <p class="writing-card-dateline">
                    {{ $writing->location }}
                    <span class="text-[var(--color-ink-subtle)]">·</span>
                    {{ $dateLong }}
                </p>
                <h3 class="writing-card-title mt-2">{{ $writing->title }}</h3>
            </div>
            <div>
                <p class="writing-card-excerpt">{{ $writing->excerpt }}</p>
                <p class="writing-card-meta mt-3">{{ $kindLabel }} · {{ $writing->read_minutes }} dakika</p>
            </div>
        </div>
    @else
        <div class="writing-card-body">
            <p class="writing-card-dateline">
                {{ $writing->location }}
                <span class="text-[var(--color-ink-subtle)]">·</span>
                {{ $dateLong }}
            </p>
            <h3 class="writing-card-title">{{ $writing->title }}</h3>
            <p class="writing-card-excerpt">{{ $writing->excerpt }}</p>
            <p class="writing-card-meta">{{ $kindLabel }} · {{ $writing->read_minutes }} dakika</p>
        </div>
    @endif
</a>
