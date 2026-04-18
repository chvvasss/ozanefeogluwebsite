@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1">
    <div>
        <p class="auth-eyebrow">Yeni şifre</p>
        <h1 class="display-fraunces">Yeni bir <em class="italic text-[var(--color-accent)]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144;">başlangıç</em>.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[42ch]">
            En az 12 karakter. Bilinen veri sızıntılarındaki şifreler otomatik reddedilir.
        </p>
    </div>

    <form method="POST" action="/reset-password" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') ?? request('token') }}">

        <div class="field">
            <label for="email" class="field-label">E-posta</label>
            <input id="email" name="email" type="email" required class="input" value="{{ old('email', request('email')) }}">
        </div>

        <div class="field">
            <label for="password" class="field-label">Yeni şifre</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required class="input">
            <p class="field-hint">12+ karakter. Parolacı bir manager kullanmanı öneririz.</p>
        </div>

        <div class="field">
            <label for="password_confirmation" class="field-label">Şifreyi onayla</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="input">
        </div>

        @if ($errors->any())
            <div class="flash flash--danger">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" class="btn btn--accent btn--lg justify-center">Şifreyi güncelle</button>
    </form>
</div>
@endsection
