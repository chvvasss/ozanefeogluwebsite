@extends('layouts.auth')

@section('content')
<div class="auth-form-wrap reveal reveal-1" x-data="{ useRecovery: false }">
    <div>
        <p class="auth-eyebrow">İki adımlı kimlik doğrulama</p>
        <h1 class="display-fraunces">Son bir <em class="italic text-[var(--color-accent)]" style="font-variation-settings: 'SOFT' 0, 'WONK' 0, 'opsz' 144;">onay</em>.</h1>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[40ch]" x-show="!useRecovery">
            Telefonundaki uygulamanın ürettiği <strong>6 haneli kodu</strong> aşağıya gir.
            Kod her 30 saniyede yenileniyor.
        </p>
        <p class="text-sm text-[var(--color-ink-muted)] mt-2 max-w-[40ch]" x-show="useRecovery" x-cloak>
            Telefonuna ulaşamıyorsan, kurtarma kodlarından <strong>bir tanesini</strong> kullan.
            Her kod tek seferlik.
        </p>
    </div>

    @if ($errors->any())
        <div class="flash flash--danger">Kod doğrulanamadı. Lütfen tekrar deneyin.</div>
    @endif

    <form method="POST" action="/two-factor-challenge" class="flex flex-col gap-5">
        @csrf

        <div class="field" x-show="!useRecovery">
            <label for="code" class="field-label">Doğrulama kodu</label>
            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                autocomplete="one-time-code"
                maxlength="6"
                x-bind:required="!useRecovery"
                x-ref="codeInput"
                class="input text-center font-mono tracking-[0.4em] text-2xl py-4"
                placeholder="000 000"
            >
        </div>

        <div class="field" x-show="useRecovery" x-cloak>
            <label for="recovery_code" class="field-label">Kurtarma kodu</label>
            <input
                id="recovery_code"
                name="recovery_code"
                type="text"
                autocomplete="off"
                x-bind:required="useRecovery"
                class="input font-mono text-center tracking-[0.2em]"
                placeholder="••••-••••-••••-••••"
            >
        </div>

        <button type="submit" class="btn btn--lg justify-center">Doğrula</button>
    </form>

    <button
        type="button"
        @click="useRecovery = !useRecovery; $nextTick(() => ($refs.codeInput && !useRecovery ? $refs.codeInput.focus() : null))"
        class="text-sm text-[var(--color-ink-muted)] hover:text-[var(--color-ink)] text-center mx-auto inline-flex items-center gap-2 no-underline border-b border-transparent hover:border-current pb-0.5 self-start"
    >
        <span x-show="!useRecovery">Kurtarma kodu kullan</span>
        <span x-show="useRecovery" x-cloak>6 haneli koda dön</span>
    </button>
</div>
@endsection
