@extends('layouts.admin', ['title' => 'İki faktör'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Güvenlik</p>
        <h1 class="admin-page-title">İki faktörlü <em class="italic text-[var(--color-accent)]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144;">doğrulama</em></h1>
        <p class="admin-page-subtitle">Telefonundan tek seferlik kod — şifren biri ele geçse bile hesabını korur.</p>
    </div>

    <div>
        @if ($enabled)
            <span class="inline-flex items-center gap-2 text-sm">
                <span class="w-2 h-2 rounded-full bg-[var(--color-success)]"></span> Etkin
            </span>
        @else
            <span class="inline-flex items-center gap-2 text-sm text-[var(--color-warning)]">
                <span class="w-2 h-2 rounded-full bg-[var(--color-warning)]"></span> Kurulmamış
            </span>
        @endif
    </div>
</header>

@if (! $enabled && ! $hasPendingSetup)
    <section class="admin-card">
        <h2 class="display-fraunces text-2xl mb-4">Adım 1 — Başlat</h2>
        <p class="text-sm text-[var(--color-ink-muted)] max-w-[62ch] mb-6 leading-relaxed">
            Başlat dediğinde sana özel bir gizli anahtar oluşturulur. Sonra telefonundaki kimlik doğrulayıcı
            (Google Authenticator, 1Password, Raivo, Ente Auth, Bitwarden…) bu anahtarla eşleşir.
        </p>
        <form method="POST" action="{{ route('admin.two-factor.enable') }}">
            @csrf
            <button type="submit" class="btn btn--accent">Kurulumu başlat</button>
        </form>
    </section>
@endif

@if ($hasPendingSetup)
    <section class="admin-card">
        <h2 class="display-fraunces text-2xl mb-4">Adım 2 — QR'ı tara</h2>
        <p class="text-sm text-[var(--color-ink-muted)] max-w-[62ch] mb-6 leading-relaxed">
            QR kodu kimlik doğrulayıcı uygulamanla tara. Uygulamanın gösterdiği 6 haneli kodu altta gir.
        </p>

        <div class="grid md:grid-cols-[auto_1fr] gap-8 items-start">
            <div class="qr-box">
                {!! $user->twoFactorQrCodeSvg() !!}
            </div>

            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)] mb-2">Elle kurulum</p>
                    <code class="block font-mono text-sm bg-[var(--color-bg-muted)] p-3 rounded break-all">
                        {{ decrypt($user->two_factor_secret) }}
                    </code>
                </div>

                <form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="flex flex-col gap-3">
                    @csrf
                    <label for="code" class="field-label">6 haneli kod</label>
                    <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" required
                           class="input text-center font-mono text-xl tracking-[0.4em]" placeholder="000 000">
                    @error('code') <p class="field-error">{{ $message }}</p> @enderror
                    <button type="submit" class="btn btn--accent mt-2">Onayla ve etkinleştir</button>
                </form>
            </div>
        </div>
    </section>
@endif

@if ($enabled && ! empty($user->two_factor_recovery_codes))
    <section class="admin-card">
        <h2 class="display-fraunces text-2xl mb-4">Kurtarma kodları</h2>
        <p class="text-sm text-[var(--color-ink-muted)] max-w-[62ch] mb-6 leading-relaxed">
            Telefonunu kaybedersen bu kodlardan biriyle giriş yapabilirsin. Her kod <strong>tek seferlik</strong>.
            Güvenli bir yere (parola yöneticisi, kilitli defter) kopyala. Aşağıdaki listeyi kimseyle paylaşma.
        </p>

        @php
            $codes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
        @endphp
        <div class="recovery-grid">
            @foreach ($codes as $code)
                <div class="recovery-code">{{ $code }}</div>
            @endforeach
        </div>

        <div class="mt-6 flex gap-2">
            <form method="POST" action="{{ route('admin.two-factor.recovery-codes') }}">
                @csrf
                <button type="submit" class="btn btn--ghost btn--sm"
                        onclick="return confirm('Yeni kodlar oluştur? Mevcut kodlar geçersiz olur.')">
                    Yeni kodlar üret
                </button>
            </form>
        </div>
    </section>

    <section class="admin-card mt-6 border-[var(--color-danger)]">
        <h2 class="display-fraunces text-2xl mb-4">Kapatma</h2>
        <p class="text-sm text-[var(--color-ink-muted)] max-w-[62ch] mb-6 leading-relaxed">
            İki faktörü kapatmak güvenliğini düşürür — gerekmedikçe kapama. Kapatmak için şifreni yeniden gir.
        </p>
        <form method="POST" action="{{ route('admin.two-factor.disable') }}" class="flex flex-col gap-3 max-w-sm">
            @csrf
            <label for="current_password" class="field-label">Mevcut şifre</label>
            <input id="current_password" name="current_password" type="password" required class="input">
            <button type="submit" class="btn btn--danger btn--sm mt-2 self-start"
                    onclick="return confirm('İki faktörü kapatmak istediğinden emin misin?')">
                İki faktörü kapat
            </button>
        </form>
    </section>
@endif
@endsection
