@extends('layouts.public', ['title' => 'Sunucu hatası · '.site_setting('identity.name')])

@section('content')
<section class="page-wrap section-y min-h-[60dvh] flex flex-col justify-center">
    <p class="eyebrow mb-6">Hata · 500</p>
    <h1 class="display-headline max-w-[20ch]" style="font-size: clamp(var(--text-4xl), 7vw, var(--text-7xl));">
        Sunucuda bir şey kırıldı.
    </h1>
    <p class="mt-8 max-w-[55ch] text-[var(--text-md)] leading-relaxed text-[var(--color-ink-muted)]">
        Kayıt tutuldu; bakılacak. Birkaç dakika sonra tekrar dene.
    </p>
    <p class="mt-4 text-xs font-mono text-[var(--color-ink-subtle)]">
        Talep No:
        <code>{{ request()->attributes->get('request_id', 'unknown') }}</code>
    </p>
    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="btn">Anasayfaya dön</a>
        <button type="button" onclick="location.reload()" class="btn btn--secondary">Sayfayı yenile</button>
    </div>
</section>
@endsection
