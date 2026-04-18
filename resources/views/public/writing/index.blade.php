@extends('layouts.public', ['title' => 'Yazılar · Ozan Efeoğlu'])

@section('content')

<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] pt-14 md:pt-24 pb-12 md:pb-16">
    <p class="eyebrow mb-4">Arşiv</p>
    <h1 class="display-fraunces text-[clamp(2.5rem,7vw,5rem)] leading-[0.98]" style="letter-spacing: var(--tracking-tightest);">
        Tüm <em class="italic text-[var(--color-accent)]">yazılar</em>.
    </h1>
    <p class="mt-6 max-w-[60ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Saha yazıları, röportajlar, köşe denemeleri ve kısa notlar.
        Türe göre süz, tarihe göre göz at.
        <a href="/feed.xml" class="underline underline-offset-4 decoration-[var(--color-accent)] decoration-2">RSS</a> ile takip edebilirsin.
    </p>
</section>

{{-- Filter bar --}}
<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-6 border-y border-[var(--color-rule)]">
    <nav class="flex flex-wrap items-center gap-3 gap-y-2" aria-label="Tür filtresi">
        <span class="eyebrow">Tür:</span>
        @foreach ($kinds as $option)
            @php
                $isActive = ($option['value'] === $filter);
                $href = $option['value']
                    ? route('writing.index', ['tur' => $option['value']])
                    : route('writing.index');
            @endphp
            <a href="{{ $href }}"
               class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs uppercase tracking-[0.15em] no-underline transition-colors duration-[var(--duration-fast)]
                      {{ $isActive
                            ? 'border-[var(--color-ink)] bg-[var(--color-ink)] text-[var(--color-bg)]'
                            : 'border-[var(--color-rule-strong)] text-[var(--color-ink-muted)] hover:border-[var(--color-ink)] hover:text-[var(--color-ink)]' }}">
                {{ $option['label'] }}
            </a>
        @endforeach

        <span class="ml-auto font-mono text-xs text-[var(--color-ink-subtle)] tabular-nums">
            {{ $writings->total() }} yazı
        </span>
    </nav>
</section>

<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-14 md:py-20">
    @if ($writings->isEmpty())
        <div class="min-h-[40dvh] flex flex-col items-center justify-center text-center">
            <p class="display-fraunces text-[clamp(2rem,4vw,3rem)] max-w-[22ch]" style="letter-spacing: var(--tracking-tighter);">
                Bu türde <em class="italic text-[var(--color-accent)]">henüz</em> bir yazı yok.
            </p>
            <p class="mt-4 text-sm text-[var(--color-ink-muted)]">
                Başka bir türü dene, ya da
                <a href="{{ route('writing.index') }}" class="underline underline-offset-4 decoration-[var(--color-accent)] decoration-2">tüm arşive</a>
                dön.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @foreach ($writings as $writing)
                @include('partials._writing-card', ['writing' => $writing, 'variant' => 'default'])
            @endforeach
        </div>

        @if ($writings->hasPages())
            <div class="mt-16 flex items-center justify-between border-t border-[var(--color-rule)] pt-6 text-sm">
                @if ($writings->onFirstPage())
                    <span class="font-mono text-xs text-[var(--color-ink-subtle)] tracking-[0.15em] uppercase">← önceki</span>
                @else
                    <a href="{{ $writings->previousPageUrl() }}" class="font-mono text-xs text-[var(--color-ink)] tracking-[0.15em] uppercase no-underline border-b border-[var(--color-ink)] pb-0.5">
                        ← önceki
                    </a>
                @endif

                <span class="font-mono text-xs text-[var(--color-ink-muted)] tabular-nums">
                    sayfa {{ $writings->currentPage() }} / {{ $writings->lastPage() }}
                </span>

                @if ($writings->hasMorePages())
                    <a href="{{ $writings->nextPageUrl() }}" class="font-mono text-xs text-[var(--color-ink)] tracking-[0.15em] uppercase no-underline border-b border-[var(--color-ink)] pb-0.5">
                        sonraki →
                    </a>
                @else
                    <span class="font-mono text-xs text-[var(--color-ink-subtle)] tracking-[0.15em] uppercase">sonraki →</span>
                @endif
            </div>
        @endif
    @endif
</section>

@endsection
