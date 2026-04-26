@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1">
    <div>
        <p class="auth-eyebrow">Şifre sıfırlama</p>
        <h1 class="display-fraunces">Unutmak <em class="italic text-[var(--color-accent)]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144;">serbest</em>.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[42ch]">
            E-posta adresini gir — bir sıfırlama bağlantısı yollayalım. Bağlantı 60 dakika geçerli.
        </p>
    </div>

    @if (session('status'))
        <div class="flash flash--success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="/forgot-password" class="flex flex-col gap-5">
        @csrf
        <div class="field">
            <label for="email" class="field-label">E-posta</label>
            <input id="email" name="email" type="email" autocomplete="username" required autofocus class="input" value="{{ old('email') }}">
        </div>
        <button type="submit" class="btn btn--lg justify-center">
            Sıfırlama bağlantısı gönder
        </button>
    </form>

    <a href="/login" class="text-sm text-[var(--color-ink-muted)] hover:text-[var(--color-ink)] no-underline border-b border-transparent hover:border-current pb-0.5 self-start">← Girişe dön</a>
</div>
@endsection
