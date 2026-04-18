@extends('layouts.public', ['title' => $writing->title.' · Ozan Efeoğlu'])

@section('content')

@php
    /** @var \App\Models\Writing $writing */
    $dateLong = optional($writing->published_at)->translatedFormat('d F Y');
    $dateYear = optional($writing->published_at)->format('Y');
    $kindLabel = $writing->kind_label;
@endphp

<article class="pb-24">

    {{-- Hero: cover + dateline + title ------------------------------------------------ --}}
    <header class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-10 md:pt-14">
        <nav class="mb-10 text-sm" aria-label="Gezinti">
            <a href="{{ route('writing.index') }}" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)] inline-flex items-center gap-1">
                <span aria-hidden="true">←</span> Yazılar
            </a>
        </nav>

        <p class="writing-card-dateline mb-4">
            {{ $writing->location }}
            <span class="text-[var(--color-ink-subtle)]">·</span>
            {{ $dateLong }}
            <span class="text-[var(--color-ink-subtle)]">·</span>
            {{ $kindLabel }}
            <span class="text-[var(--color-ink-subtle)]">·</span>
            {{ $writing->read_minutes }} dakikalık okuma
        </p>

        <h1 class="display-fraunces leading-[1.02] max-w-[22ch]"
            style="font-size: clamp(var(--text-4xl), 7vw, var(--text-7xl)); letter-spacing: var(--tracking-tightest); font-variation-settings: 'SOFT' 20, 'WONK' 0, 'opsz' 144;">
            {{ $writing->title }}
        </h1>

        @if ($writing->excerpt)
            <p class="mt-8 max-w-[58ch] text-[var(--text-lg)] leading-relaxed text-[var(--color-ink-muted)]">
                {{ $writing->excerpt }}
            </p>
        @endif
    </header>

    {{-- Cover image (placeholder) ----------------------------------------------------- --}}
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] mt-12 md:mt-16">
        <figure class="cover-placeholder rounded-[var(--radius-lg)]"
                style="--hue-a: {{ $writing->cover_hue_a }}; --hue-b: {{ $writing->cover_hue_b }}; aspect-ratio: 16 / 9;">
            <span class="cover-label">{{ strtoupper((string) $writing->location) }} · {{ optional($writing->published_at)->format('Y-m') }}</span>
            <span class="cover-kind">{{ $kindLabel }}</span>
        </figure>
    </div>

    {{-- Prose body -------------------------------------------------------------------- --}}
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] mt-14 md:mt-20 grid grid-cols-1 lg:grid-cols-[1fr_minmax(0,65ch)_1fr] gap-8">

        <aside class="hidden lg:block">
            <dl class="sticky top-32 marginalia space-y-3">
                <div><dt class="text-[var(--color-ink-subtle)]">yayım</dt><dd class="mt-1 tabular-nums">{{ optional($writing->published_at)->format('Y-m-d') }}</dd></div>
                <div><dt class="text-[var(--color-ink-subtle)]">konum</dt><dd class="mt-1">{{ $writing->location ?? '—' }}</dd></div>
                <div><dt class="text-[var(--color-ink-subtle)]">tür</dt><dd class="mt-1">{{ $kindLabel }}</dd></div>
                <div><dt class="text-[var(--color-ink-subtle)]">okuma</dt><dd class="mt-1 tabular-nums">{{ $writing->read_minutes }} dk</dd></div>
                @if ($writing->publications->isNotEmpty())
                    <div class="pt-2 border-t border-[var(--color-rule)]">
                        <dt class="text-[var(--color-ink-subtle)]">yayın</dt>
                        @foreach ($writing->publications as $pub)
                            <dd class="mt-1">
                                @if ($pub->pivot->link)
                                    <a href="{{ $pub->pivot->link }}" target="_blank" rel="noopener" class="no-underline border-b border-[var(--color-rule-strong)] hover:border-[var(--color-ink)] pb-0.5">{{ $pub->name }}</a>
                                @else
                                    {{ $pub->name }}
                                @endif
                            </dd>
                        @endforeach
                    </div>
                @endif
            </dl>
        </aside>

        <div class="prose-article">
            {!! $writing->body !!}
        </div>

        <div class="hidden lg:block"></div>
    </div>

    {{-- Publications (mobile) --------------------------------------------------------- --}}
    @if ($writing->publications->isNotEmpty())
        <div class="lg:hidden max-w-[var(--container-narrow)] mx-auto px-[clamp(1rem,4vw,3rem)] mt-10 pt-6 border-t border-[var(--color-rule)]">
            <p class="eyebrow mb-3">Yayın</p>
            <ul class="flex flex-wrap gap-3 text-sm">
                @foreach ($writing->publications as $pub)
                    <li>
                        @if ($pub->pivot->link)
                            <a href="{{ $pub->pivot->link }}" class="no-underline border-b border-[var(--color-rule-strong)] hover:border-[var(--color-ink)] pb-0.5">{{ $pub->name }}</a>
                        @else
                            <span>{{ $pub->name }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Prev / next navigation -------------------------------------------------------- --}}
    <nav class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] mt-20 md:mt-28 border-t border-[var(--color-rule)] pt-8 grid gap-6 md:grid-cols-2" aria-label="Yazı gezintisi">
        <div>
            @if ($prev)
                <a href="{{ $prev->url() }}" class="group block no-underline">
                    <span class="font-mono text-xs uppercase tracking-[0.2em] text-[var(--color-ink-subtle)]">← önceki yazı</span>
                    <span class="display-fraunces block mt-2 text-xl md:text-2xl leading-[1.15] group-hover:text-[var(--color-accent)] transition-colors">
                        {{ $prev->title }}
                    </span>
                </a>
            @endif
        </div>
        <div class="md:text-right">
            @if ($next)
                <a href="{{ $next->url() }}" class="group block no-underline">
                    <span class="font-mono text-xs uppercase tracking-[0.2em] text-[var(--color-ink-subtle)]">sonraki yazı →</span>
                    <span class="display-fraunces block mt-2 text-xl md:text-2xl leading-[1.15] group-hover:text-[var(--color-accent)] transition-colors">
                        {{ $next->title }}
                    </span>
                </a>
            @endif
        </div>
    </nav>

    {{-- Related ----------------------------------------------------------------------- --}}
    @if ($related->isNotEmpty())
        <section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] mt-20 md:mt-32">
            <p class="eyebrow mb-5">Benzer yazılar</p>
            <h2 class="display-fraunces text-[clamp(2rem,4vw,3rem)] leading-none mb-10" style="letter-spacing: var(--tracking-tighter);">
                Aynı <em class="italic text-[var(--color-accent)]">damarda</em>.
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($related as $item)
                    @include('partials._writing-card', ['writing' => $item, 'variant' => 'default'])
                @endforeach
            </div>
        </section>
    @endif

</article>

@endsection
