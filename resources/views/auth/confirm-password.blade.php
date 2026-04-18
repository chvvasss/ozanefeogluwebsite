@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1">
    <div>
        <p class="auth-eyebrow">Hassas işlem</p>
        <h1 class="display-fraunces">Şifreyi <em class="italic text-[var(--color-accent)]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144;">yeniden</em> gir.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[42ch]">
            Güvenlik için hassas alanlara girmeden kısa bir onay.
        </p>
    </div>

    <form method="POST" action="/user/confirm-password" class="flex flex-col gap-5">
        @csrf
        <div class="field">
            <label for="password" class="field-label">Şifre</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required autofocus class="input">
        </div>
        @if ($errors->any())
            <div class="flash flash--danger">Şifre eşleşmedi.</div>
        @endif
        <button type="submit" class="btn btn--accent btn--lg justify-center">Onayla</button>
    </form>
</div>
@endsection
