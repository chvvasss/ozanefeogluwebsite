@extends('layouts.public')

@section('content')
<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] min-h-[70dvh] flex flex-col justify-center py-20">
    <p class="eyebrow mb-6">Hata · 404</p>
    <h1 class="display-fraunces text-[clamp(3rem,10vw,7rem)] leading-[0.9]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144; letter-spacing: var(--tracking-tightest);">
        Aradığın sayfa <em class="italic text-[var(--color-accent)]">kaybolmuş</em>.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Bu URL ile eşleşen bir kayıt yok. Taşınmış olabilir, silinmiş olabilir, ya da hiç var olmamış olabilir.
        İstersen anasayfaya dön ya da aşağıdan ara.
    </p>
    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="btn btn--accent">Anasayfaya dön</a>
        <a href="#writing" class="btn btn--ghost">Yazılara göz at</a>
    </div>
</section>
@endsection
