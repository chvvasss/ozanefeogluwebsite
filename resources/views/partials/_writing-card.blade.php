@props(['writing', 'variant' => 'default'])

@php
    /** @var \App\Models\Writing $writing */
    $href = $writing->url();
    $kindLabel = $writing->kind_label;
    $locationUpper = strtoupper((string) ($writing->location ?? ''));
    $dateShort = optional($writing->published_at)->format('Y-m');
    $dateLong = optional($writing->published_at)->translatedFormat('d F Y');
    $coverRadius = $variant === 'hero' ? 'var(--radius-lg)' : 'var(--radius-md)';
@endphp

<a href="{{ $href }}"
   class="writing-card {{ $variant === 'hero' ? 'writing-card--hero block' : 'scroll-reveal' }}"
   aria-label="{{ $writing->title }}">
    <figure class="cover-placeholder"
            style="--hue-a: {{ $writing->cover_hue_a }}; --hue-b: {{ $writing->cover_hue_b }}; border-radius: {{ $coverRadius }};">
        <span class="cover-label">{{ $locationUpper }} · {{ $dateShort }}</span>
        <span class="cover-kind">{{ $kindLabel }}</span>
    </figure>

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
