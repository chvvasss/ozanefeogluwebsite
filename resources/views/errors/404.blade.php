@extends('layouts.public', ['title' => 'Sayfa bulunamadı · '.site_setting('identity.name')])

@section('content')
<section class="page-wrap section-y min-h-[60dvh] flex flex-col justify-center">
    <p class="eyebrow mb-6">Hata · 404</p>
    <h1 class="display-headline max-w-[20ch]" style="font-size: clamp(var(--text-4xl), 7vw, var(--text-7xl));">
        Bu sayfa yok.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Bu URL ile eşleşen bir kayıt yok. Taşınmış, silinmiş ya da hiç var olmamış olabilir.
    </p>
    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="btn">Anasayfaya dön</a>
        <a href="{{ route('writing.index') }}" class="btn btn--secondary">Yazılara göz at</a>
    </div>
</section>
@endsection
