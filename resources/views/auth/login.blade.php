@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1">
    <div>
        <p class="auth-eyebrow">Yazı masası</p>
        <h1 class="display-fraunces">Yine <em class="italic text-[var(--color-accent)]">açık</em>.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[42ch]">
            Özel masa. Şifreni unuttuysan panik yok — aşağıdaki bağlantıyı kullan.
        </p>
    </div>

    @if ($errors->any())
        <div class="flash flash--danger" role="alert">
            <strong class="font-semibold">Giriş yapılamadı.</strong>
            <span>Geçersiz e-posta veya şifre. Lütfen tekrar deneyin.</span>
        </div>
    @endif

    @if (session('status'))
        <div class="flash flash--success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="/login" class="flex flex-col gap-5" novalidate>
        @csrf

        <div class="field">
            <label for="email" class="field-label">E-posta</label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="username"
                autofocus
                required
                value="{{ old('email') }}"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="email-hint"
                class="input"
            >
            <p id="email-hint" class="field-hint">Atölye kimliğiyle eşleşen adres.</p>
        </div>

        <div class="field">
            <div class="flex items-baseline justify-between">
                <label for="password" class="field-label">Şifre</label>
                <a href="/forgot-password" class="text-xs text-[var(--color-ink-subtle)] hover:text-[var(--color-ink)] no-underline border-b border-transparent hover:border-current pb-0.5">
                    Şifremi unuttum
                </a>
            </div>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                class="input"
            >
        </div>

        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
            <input type="checkbox" name="remember" class="accent-[var(--color-accent)]">
            <span class="text-[var(--color-ink-muted)]">Beni hatırla</span>
        </label>

        <button type="submit" class="btn btn--accent btn--lg justify-center mt-2">
            Devam et
            <span aria-hidden="true">→</span>
        </button>
    </form>

    <p class="text-xs text-[var(--color-ink-subtle)] border-t border-[var(--color-rule)] pt-6 leading-relaxed">
        5 başarısız girişten sonra 1 dakika bekleme süresi devreye girer. Giriş denemeleri kayıt altındadır.
    </p>
</div>
@endsection
