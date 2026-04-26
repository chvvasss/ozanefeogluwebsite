@extends('layouts.admin', ['title' => 'Gezinme & Hero · Ayarlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Ana menü davranışı ve hero buton etiketleri. Hero mod seçimi (B.3) ayrı panelde açılacak.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Gezinme</h2>

        <div class="field">
            <label class="field-label inline-flex items-center gap-3">
                <input type="hidden" name="nav[show_visuals]" value="0">
                <input type="checkbox" name="nav[show_visuals]" value="1"
                       class="input-checkbox"
                       @checked(old('nav.show_visuals', $values['nav.show_visuals']))>
                Görseller bağlantısını göster
            </label>
            @error('nav.show_visuals') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Kapalıyken ana menüden <em>Görseller</em> gizlenir (arşiv sayfası hazır olana dek).</p>
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Hero · metinler & butonlar</h2>

        <div class="field">
            <label for="hero_eyebrow" class="field-label">Hero eyebrow</label>
            <input id="hero_eyebrow" name="hero[eyebrow]" type="text" maxlength="120"
                   class="input"
                   value="{{ old('hero.eyebrow', $values['hero.eyebrow']) }}">
            @error('hero.eyebrow') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Hero bloğunun üst etiketi. Örn. <em>Haber Masası · İstanbul</em>.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="field">
                <label for="hero_cta1_label" class="field-label">Birincil buton · etiket</label>
                <input id="hero_cta1_label" name="hero[cta_primary_label]" type="text" maxlength="40"
                       class="input"
                       value="{{ old('hero.cta_primary_label', $values['hero.cta_primary_label']) }}">
                @error('hero.cta_primary_label') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="hero_cta1_url" class="field-label">Birincil buton · URL</label>
                <input id="hero_cta1_url" name="hero[cta_primary_url]" type="text" maxlength="200"
                       class="input font-mono"
                       placeholder="/yazilar"
                       value="{{ old('hero.cta_primary_url', $values['hero.cta_primary_url']) }}">
                @error('hero.cta_primary_url') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="hero_cta2_label" class="field-label">İkincil buton · etiket</label>
                <input id="hero_cta2_label" name="hero[cta_secondary_label]" type="text" maxlength="40"
                       class="input"
                       value="{{ old('hero.cta_secondary_label', $values['hero.cta_secondary_label']) }}">
                @error('hero.cta_secondary_label') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="hero_cta2_url" class="field-label">İkincil buton · URL</label>
                <input id="hero_cta2_url" name="hero[cta_secondary_url]" type="text" maxlength="200"
                       class="input font-mono"
                       placeholder="/hakkimda"
                       value="{{ old('hero.cta_secondary_url', $values['hero.cta_secondary_url']) }}">
                @error('hero.cta_secondary_url') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <p class="field-hint">URL alanları dahili yollar (<code>/yazilar</code>) veya tam https adresi olabilir. Boş bırakırsan buton gizlenir.</p>
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">Gezinme ayarlarını kaydet</button>
    </div>
</form>
@endsection
