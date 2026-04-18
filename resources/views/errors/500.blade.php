@extends('layouts.public')

@section('content')
<section class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] min-h-[70dvh] flex flex-col justify-center py-20">
    <p class="eyebrow mb-6">Hata · 500</p>
    <h1 class="display-fraunces text-[clamp(3rem,10vw,7rem)] leading-[0.9]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144; letter-spacing: var(--tracking-tightest);">
        Bir şey <em class="italic text-[var(--color-accent)]">kırıldı</em>.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Sunucuda beklenmedik bir hata oluştu. Kayıt tutuldu, bakılacak.
        Birkaç dakika sonra tekrar dene — büyük ihtimalle düzelmiş olur.
    </p>
    <p class="mt-4 text-xs font-mono text-[var(--color-ink-subtle)]">
        Talep No:
        <code>{{ request()->attributes->get('request_id', 'unknown') }}</code>
    </p>
    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="btn btn--accent">Anasayfaya dön</a>
        <button type="button" onclick="location.reload()" class="btn btn--ghost">Sayfayı yenile</button>
    </div>
</section>
@endsection
