@extends('layouts.public', ['title' => 'Bakım modu · '.site_setting('identity.name')])

@section('content')
<section class="page-wrap section-y min-h-[60dvh] flex flex-col justify-center">
    <p class="eyebrow mb-6">Bakım modu · 503</p>
    <h1 class="display-headline max-w-[22ch]" style="font-size: clamp(var(--text-4xl), 7vw, var(--text-7xl));">
        Kısa bir bakım var.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Site bir süreliğine kapalı. Birazdan tekrar açılacak.
    </p>
</section>
@endsection
