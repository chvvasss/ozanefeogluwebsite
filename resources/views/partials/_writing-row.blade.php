@props(['writing'])

@php
    /** @var \App\Models\Writing $writing */
    $href = $writing->url();
    $location = strtoupper((string) ($writing->location ?? ''));
    $monthDay = optional($writing->published_at)->format('d.m');
    $kindLabel = $writing->kind_label;
    $hasCover = method_exists($writing, 'hasCover') && $writing->hasCover();
@endphp

<a href="{{ $href }}" class="writing-row @if ($hasCover) writing-row--has-thumb @endif" aria-label="{{ $writing->title }}">
    <div class="writing-row-dateline">
        <span class="block tabular-nums">{{ $monthDay }}</span>
        @if ($location)
            <span class="block mt-0.5 text-[var(--color-ink-subtle)]">{{ $location }}</span>
        @endif
    </div>

    @if ($hasCover)
        <figure class="writing-row-thumb">
            <img src="{{ $writing->coverUrl('w640') ?? $writing->coverUrl() }}"
                 alt="{{ $writing->title }}"
                 loading="lazy"
                 width="240" height="160">
        </figure>
    @endif

    <div class="writing-row-body">
        <span class="writing-row-kind">
            <span class="md:hidden tabular-nums">{{ $monthDay }}@if ($location) · {{ $location }}@endif · </span>{{ $kindLabel }} · {{ $writing->read_minutes }} dk
        </span>
        <h3 class="writing-row-title">{{ $writing->title }}</h3>
        @if ($writing->excerpt)
            <p class="writing-row-lede">{{ $writing->excerpt }}</p>
        @endif
    </div>
</a>
