@extends('layouts.admin', ['title' => 'Profil'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Hesap</p>
        <h1 class="admin-page-title">Profil</h1>
        <p class="admin-page-subtitle">Adın, e-posta adresin ve tercih ettiğin dil.</p>
    </div>
</header>

<div class="grid gap-6 md:grid-cols-[2fr_1fr]">
    <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-card flex flex-col gap-5">
        @csrf

        <div class="field">
            <label for="name" class="field-label">İsim</label>
            <input id="name" name="name" type="text" required class="input" value="{{ old('name', $user->name) }}">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="email" class="field-label">E-posta</label>
            <input id="email" name="email" type="email" required class="input" value="{{ old('email', $user->email) }}">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="locale" class="field-label">Arayüz dili</label>
            <select id="locale" name="locale" class="input">
                <option value="tr" @selected(old('locale', $user->locale) === 'tr')>Türkçe</option>
                <option value="en" @selected(old('locale', $user->locale) === 'en')>English</option>
            </select>
        </div>

        <div class="flex items-center justify-end gap-2 mt-2">
            <button type="submit" class="btn btn--accent">Değişiklikleri kaydet</button>
        </div>
    </form>

    <aside class="admin-card">
        <p class="admin-card-title">Güvenlik özeti</p>
        <dl class="text-sm space-y-4">
            <div>
                <dt class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)]">İki faktör</dt>
                <dd class="mt-1">
                    @if ($user->hasTwoFactorEnabled())
                        <span class="inline-flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-success)]"></span> Etkin
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 text-[var(--color-warning)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-warning)]"></span> Kurulmamış
                        </span>
                        <a href="{{ route('admin.two-factor.setup') }}" class="block mt-2 text-xs underline underline-offset-4">Şimdi kur →</a>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)]">Son giriş</dt>
                <dd class="mt-1 font-mono text-xs">
                    {{ optional($user->last_login_at)->format('Y-m-d H:i') ?? '—' }}<br>
                    {{ $user->last_login_ip ?? '' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wider text-[var(--color-ink-subtle)]">Şifre değiştirildi</dt>
                <dd class="mt-1 font-mono text-xs">
                    {{ optional($user->password_changed_at)->diffForHumans() ?? 'bilinmiyor' }}
                </dd>
            </div>
        </dl>
    </aside>
</div>
@endsection
