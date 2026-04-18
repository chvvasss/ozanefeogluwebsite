@extends('layouts.public')

@section('content')
<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] min-h-[70dvh] flex flex-col justify-center py-20">
    <p class="eyebrow mb-6">Bakım modu · 503</p>
    <h1 class="display-fraunces text-[clamp(3rem,10vw,7rem)] leading-[0.9]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144; letter-spacing: var(--tracking-tightest);">
        Kısa bir <em class="italic text-[var(--color-accent)]">bakım</em> var.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Atölye bir süreliğine kapalı. Bir şeyleri taşıyoruz, değiştiriyoruz, rafa koyuyoruz.
        Birazdan tekrar açılacak — bir şey kaybolmuyor.
    </p>
</section>
@endsection
