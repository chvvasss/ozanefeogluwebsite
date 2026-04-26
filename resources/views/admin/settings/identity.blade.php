@extends('layouts.admin', ['title' => 'Kimlik · Ayarlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Site kimliği, iletişim kanalları ve gezinme. Tüm değişiklikler anında yayına alınır; önbellek otomatik temizlenir.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Kimlik</h2>

        <div class="field">
            <label for="identity_name" class="field-label">İsim</label>
            <input id="identity_name" name="identity[name]" type="text" required maxlength="120"
                   class="input"
                   value="{{ old('identity.name', $values['identity.name']) }}">
            @error('identity.name') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Tam ad; hero ve metadata için kullanılır.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="field">
                <label for="identity_role_primary" class="field-label">Birincil rol</label>
                <input id="identity_role_primary" name="identity[role_primary]" type="text" required maxlength="120"
                       class="input"
                       value="{{ old('identity.role_primary', $values['identity.role_primary']) }}">
                @error('identity.role_primary') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Örn. <em>Foto muhabir</em>.</p>
            </div>

            <div class="field">
                <label for="identity_base" class="field-label">Merkez</label>
                <input id="identity_base" name="identity[base]" type="text" maxlength="80"
                       class="input"
                       value="{{ old('identity.base', $values['identity.base']) }}">
                @error('identity.base') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Şehir, bölge. Örn. <em>İstanbul</em>.</p>
            </div>
        </div>

        <div class="field">
            <label for="identity_role_secondary" class="field-label">İkincil rol</label>
            <input id="identity_role_secondary" name="identity[role_secondary]" type="text" maxlength="160"
                   class="input"
                   value="{{ old('identity.role_secondary', $values['identity.role_secondary']) }}">
            @error('identity.role_secondary') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Örn. <em>editör · yayıncı</em>.</p>
        </div>

        <div class="field">
            <label for="identity_role_tertiary" class="field-label">Üçüncül rol (uzmanlık)</label>
            <input id="identity_role_tertiary" name="identity[role_tertiary]" type="text" maxlength="200"
                   class="input"
                   value="{{ old('identity.role_tertiary', $values['identity.role_tertiary']) }}">
            @error('identity.role_tertiary') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Örn. <em>Drone haberciliği · Görsel göstergebilim</em>.</p>
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Kurum bağı</h2>

        <div class="field">
            <label for="identity_affiliation" class="field-label">Bağlı kurum (tam metin)</label>
            <input id="identity_affiliation" name="identity[affiliation]" type="text" maxlength="200"
                   class="input"
                   value="{{ old('identity.affiliation', $values['identity.affiliation']) }}">
            @error('identity.affiliation') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Örn. <em>Anadolu Ajansı · Uluslararası Haber Merkezi · İstanbul</em>.</p>
        </div>

        <div class="field">
            <label class="field-label inline-flex items-center gap-3">
                <input type="hidden" name="identity[affiliation_approved]" value="0">
                <input type="checkbox" name="identity[affiliation_approved]" value="1"
                       class="input-checkbox"
                       @checked(old('identity.affiliation_approved', $values['identity.affiliation_approved']))>
                Kurum onayı alındı
            </label>
            @error('identity.affiliation_approved') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">İşaretli değilken kurum rozeti ve afiliyasyon satırı sitede <strong>gizlenir</strong>. KVKK/kurumsal risk eşiği.</p>
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Kısa metinler</h2>

        <div class="field">
            <label for="identity_description" class="field-label">Meta açıklama (SEO)</label>
            <textarea id="identity_description" name="identity[description]" rows="3" maxlength="400"
                      class="input">{{ old('identity.description', $values['identity.description']) }}</textarea>
            @error('identity.description') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Google/Bing açıklaması, OG fallback. 160 karakteri geçme önerilir.</p>
        </div>

        <div class="field">
            <label for="identity_manifesto_quote" class="field-label">Manifesto cümlesi</label>
            <input id="identity_manifesto_quote" name="identity[manifesto_quote]" type="text" maxlength="200"
                   class="input"
                   value="{{ old('identity.manifesto_quote', $values['identity.manifesto_quote']) }}">
            @error('identity.manifesto_quote') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Hero altı / hakkımda üstü epigraf.</p>
        </div>

        <div class="field">
            <label for="identity_current_context" class="field-label">Şu an · kısa durum</label>
            <input id="identity_current_context" name="identity[current_context]" type="text" maxlength="120"
                   class="input"
                   value="{{ old('identity.current_context', $values['identity.current_context']) }}">
            @error('identity.current_context') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Örn. <em>Şu an · Haber Masası · İstanbul</em>.</p>
        </div>
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">Kimlik ayarlarını kaydet</button>
    </div>
</form>
@endsection
