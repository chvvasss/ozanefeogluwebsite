@extends('layouts.public', ['title' => 'Yazılar · '.site_setting('identity.name')])

@section('content')

@php
    /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \App\Models\Writing>> $writingsByYear */
    /** @var int $totalCount */
    /** @var string|null $filter */
    /** @var array<int, array{value: string|null, label: string}> $kinds */
    $activeKindLabel = collect($kinds)->firstWhere('value', $filter)['label'] ?? 'tümü';
@endphp

{{-- =============================== INDEX HEADER =============================== --}}
<section class="border-b border-[var(--color-rule)]">
    <div class="page-wrap pt-12 md:pt-20 pb-10 md:pb-14">
        <p class="eyebrow mb-4">Arşiv</p>
        <h1 class="display-headline" style="font-size: clamp(var(--text-4xl), 6vw, var(--text-6xl));">
            Tüm yazılar
        </h1>
        <p class="mt-6 max-w-[58ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
            Saha yazıları, röportajlar, denemeler ve kısa notlar — yayım tarihine göre, yıl yıl.
        </p>
        <p class="mt-6 dateline tabular-nums">
            {{ $totalCount }} yazı
            <span class="dateline-separator">·</span>
            tür: {{ $activeKindLabel }}
        </p>
    </div>
</section>

{{-- =============================== ARCHIVE BODY =============================== --}}
<section class="page-wrap py-12 md:py-16">
    <div class="dossier-grid">

        {{-- Filter rail --}}
        <aside class="dg-3 lg:sticky lg:top-32 lg:self-start">
            <p class="eyebrow mb-4">Tür</p>
            <ul class="space-y-1.5 text-sm">
                @foreach ($kinds as $option)
                    @php
                        $isActive = $option['value'] === $filter;
                        $href = $option['value']
                            ? route('writing.index', ['tur' => $option['value']])
                            : route('writing.index');
                    @endphp
                    <li>
                        <a href="{{ $href }}"
                           class="no-underline {{ $isActive ? 'text-[var(--color-ink)] border-b border-[var(--color-ink)]' : 'text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]' }} pb-0.5"
                           @if ($isActive) aria-current="page" @endif>
                            {{ $option['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- Year-grouped chronological list --}}
        <div class="dg-9">
            @if ($writingsByYear->isEmpty())
                <p class="dateline">
                    Bu türde yayımlanmış bir yazı yok.
                    @if ($filter)
                        <a href="{{ route('writing.index') }}" class="link-quiet ml-2">Tüm türlere dön →</a>
                    @endif
                </p>
            @else
                @foreach ($writingsByYear as $year => $entries)
                    <h2 class="year-label tabular-nums">{{ $year }}</h2>
                    @foreach ($entries as $writing)
                        @include('partials._writing-row', ['writing' => $writing])
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
</section>

@endsection
