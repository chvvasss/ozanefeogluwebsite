@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1">
    <div>
        <p class="auth-eyebrow">E-posta doğrulama</p>
        <h1 class="display-fraunces">Bir <em class="italic text-[var(--color-accent)]">posta</em> bekliyor.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[48ch]">
            Hesabını kullanmaya başlamak için <strong>{{ auth()->user()?->email }}</strong> adresine gönderdiğimiz doğrulama bağlantısını tıklaman gerekiyor.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="flash flash--success" role="status">
            Yeni bir doğrulama bağlantısı gönderildi.
        </div>
    @endif

    @if (session('status') && session('status') !== 'verification-link-sent')
        <div class="flash flash--success">{{ session('status') }}</div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">
                Doğrulama bağlantısını yeniden gönder
            </button>
        </form>

        <form method="POST" action="/logout" class="text-center">
            @csrf
            <button type="submit" class="link-quiet text-sm text-[var(--color-ink-muted)]">
                Farklı bir hesapla giriş yap
            </button>
        </form>
    </div>

    <p class="auth-mute mt-6 text-xs text-[var(--color-ink-subtle)]">
        Mailini bulamıyorsan, spam klasörüne bak. E-posta adresin yanlışsa, çıkış yap ve doğru hesapla giriş yapmayı dene.
    </p>
</div>
@endsection
