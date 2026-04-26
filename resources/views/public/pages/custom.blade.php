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
    $eyebrow = $page->extra('eyebrow', 'Sayfa');
@endphp

<article>
    <section class="scene scene--overture">
        <div class="page-wrap-narrow">
            <p class="eyebrow mb-5">{{ $eyebrow }}</p>
            <h1 class="display-editorial" style="font-size: clamp(var(--text-3xl), 5vw, var(--text-5xl));">
                {{ $page->title }}
            </h1>
            @if ($page->intro)
                <p class="mt-6 text-[var(--text-md)] leading-[1.7] text-[var(--color-ink-muted)] max-w-[62ch]">
                    {{ $page->intro }}
                </p>
            @endif
        </div>
    </section>

    <section class="scene scene--muted border-t border-[var(--color-rule)]">
        <div class="page-wrap-narrow">
            <div class="prose-article">
                {!! $page->body !!}
            </div>
        </div>
    </section>
</article>

@endsection
