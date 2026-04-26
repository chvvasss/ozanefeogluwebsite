@extends('layouts.admin', ['title' => 'İletişim · Ayarlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Halka açık iletişim kanalları ve mesaj saklama politikası.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Kanallar</h2>

        <div class="field">
            <label for="contact_email" class="field-label">E-posta</label>
            <input id="contact_email" name="contact[email]" type="email" required maxlength="160"
                   class="input"
                   value="{{ old('contact.email', $values['contact.email']) }}">
            @error('contact.email') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">İletişim sayfasında ve footer'da görünür.</p>
        </div>

        <div class="field">
            <label for="contact_signal" class="field-label">Signal bağlantısı</label>
            <input id="contact_signal" name="contact[signal_url]" type="url" maxlength="200"
                   class="input"
                   placeholder="https://signal.me/#eu/..."
                   value="{{ old('contact.signal_url', $values['contact.signal_url']) }}">
            @error('contact.signal_url') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Boş bırakılırsa iletişim sayfasında Signal rozeti gizlenir.</p>
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">PGP (opsiyonel)</h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="field">
                <label for="contact_pgp_keyid" class="field-label">Key ID (kısa)</label>
                <input id="contact_pgp_keyid" name="contact[pgp_key_id]" type="text" maxlength="40"
                       class="input font-mono"
                       placeholder="0xABCD1234"
                       value="{{ old('contact.pgp_key_id', $values['contact.pgp_key_id']) }}">
                @error('contact.pgp_key_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="contact_pgp_fingerprint" class="field-label">Parmak izi</label>
                <input id="contact_pgp_fingerprint" name="contact[pgp_fingerprint]" type="text" maxlength="80"
                       class="input font-mono"
                       placeholder="AAAA BBBB CCCC DDDD EEEE  FFFF 1111 2222 3333 4444"
                       value="{{ old('contact.pgp_fingerprint', $values['contact.pgp_fingerprint']) }}">
                @error('contact.pgp_fingerprint') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="field">
            <label for="contact_pgp_download" class="field-label">Açık anahtar indirme URL</label>
            <input id="contact_pgp_download" name="contact[pgp_download]" type="url" maxlength="200"
                   class="input"
                   placeholder="https://ozanefeoglu.com/pgp.asc"
                   value="{{ old('contact.pgp_download', $values['contact.pgp_download']) }}">
            @error('contact.pgp_download') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Üç alan da doluysa iletişim sayfasında PGP bloğu görünür.</p>
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Mesaj saklama</h2>

        <div class="field">
            <label for="contact_retention" class="field-label">Saklama süresi (gün)</label>
            <input id="contact_retention" name="contact[retention_days]" type="number" min="1" max="365" required
                   class="input max-w-[10rem]"
                   value="{{ old('contact.retention_days', $values['contact.retention_days']) }}">
            @error('contact.retention_days') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">
                Yeni gelen mesajlar için saklama süresi. KVKK asgari ilke: gerekmediğinde sakla<strong>ma</strong>.
                Varsayılan 90 gün. Bu süre geçmiş mesajlar otomatik silinir (cron komutu ayrıca devreye girer).
            </p>
        </div>
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">İletişim ayarlarını kaydet</button>
    </div>
</form>
@endsection
