@php
    /** @var \App\Models\Photo $photo */
    /** @var \Illuminate\Support\Collection $writings */
    $isNew = ! $photo->exists;
@endphp

<form method="POST" enctype="multipart/form-data"
      action="{{ $isNew ? route('admin.photos.store') : route('admin.photos.update', $photo) }}"
      class="grid gap-6 md:grid-cols-[2fr_1fr]">
    @csrf
    @unless ($isNew) @method('PUT') @endunless

    {{-- Main column --}}
    <div class="admin-card flex flex-col gap-5">
        <section class="flex flex-col gap-5">
            <h2 class="admin-card-title">Temel</h2>

            <div class="field">
                <label for="title_tr" class="field-label">Başlık (TR)</label>
                <input id="title_tr" name="title_tr" type="text" required maxlength="200"
                       class="input"
                       value="{{ old('title_tr', $photo->getTranslation('title', 'tr', false)) }}">
                @error('title_tr') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="field">
                    <label for="slug_tr" class="field-label">Kısa ad (URL)</label>
                    <input id="slug_tr" name="slug_tr" type="text" maxlength="80"
                           class="input font-mono"
                           pattern="[a-z0-9\-]+"
                           placeholder="boş bırakırsan başlıktan üretilir"
                           value="{{ old('slug_tr', $photo->getTranslation('slug', 'tr', false)) }}">
                    @error('slug_tr') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="kind" class="field-label">Tür</label>
                    <select id="kind" name="kind" required class="input">
                        @foreach (App\Models\Photo::KINDS as $k)
                            <option value="{{ $k }}" @selected(old('kind', $photo->kind) === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                    @error('kind') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="field">
                <label for="caption_tr" class="field-label">Alt yazı (TR)</label>
                <textarea id="caption_tr" name="caption_tr" rows="3" maxlength="500"
                          class="input">{{ old('caption_tr', $photo->getTranslation('caption', 'tr', false)) }}</textarea>
                @error('caption_tr') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Fotoğrafın hikâyesi / bağlamı. 2–3 cümle yeterli.</p>
            </div>

            <div class="field">
                <label for="alt_text_tr" class="field-label">Alt metin (erişilebilirlik)</label>
                <input id="alt_text_tr" name="alt_text_tr" type="text" maxlength="300"
                       class="input"
                       value="{{ old('alt_text_tr', $photo->getTranslation('alt_text', 'tr', false)) }}">
                @error('alt_text_tr') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Ekran okuyucular için görünenin düz anlatımı.</p>
            </div>
        </section>

        <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
            <h2 class="admin-card-title">Konum & zaman</h2>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="field">
                    <label for="location" class="field-label">Konum</label>
                    <input id="location" name="location" type="text" maxlength="160"
                           class="input"
                           placeholder="örn. Üsküdar, İstanbul"
                           value="{{ old('location', $photo->location) }}">
                    @error('location') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="captured_at" class="field-label">Çekim tarihi</label>
                    <input id="captured_at" name="captured_at" type="date"
                           class="input"
                           value="{{ old('captured_at', optional($photo->captured_at)->format('Y-m-d')) }}">
                    @error('captured_at') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
            <h2 class="admin-card-title">Haklar & künye</h2>

            <div class="field">
                <label for="credit" class="field-label">Künye</label>
                <input id="credit" name="credit" type="text" maxlength="160"
                       class="input"
                       placeholder="{{ site_setting('photo.default_credit', 'Foto: Ozan Efeoğlu / AA') }}"
                       value="{{ old('credit', $photo->credit) }}">
                @error('credit') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Boş bırakırsan site varsayılanı kullanılır.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="field">
                    <label for="source" class="field-label">Kaynak</label>
                    <input id="source" name="source" type="text" maxlength="120"
                           class="input"
                           placeholder="AA, kişisel, freelance…"
                           value="{{ old('source', $photo->source) }}">
                    @error('source') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="license" class="field-label">Lisans</label>
                    <select id="license" name="license" class="input">
                        <option value="">—</option>
                        @foreach (App\Models\Photo::LICENSES as $l)
                            <option value="{{ $l }}" @selected(old('license', $photo->license) === $l)>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('license') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="field">
                <label for="rights_notes" class="field-label">Hak notları</label>
                <textarea id="rights_notes" name="rights_notes" rows="2" maxlength="300"
                          class="input">{{ old('rights_notes', $photo->rights_notes) }}</textarea>
                @error('rights_notes') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
            <h2 class="admin-card-title">Bağlantılar</h2>
            <div class="field">
                <label for="writing_id" class="field-label">İlgili yazı (opsiyonel)</label>
                <select id="writing_id" name="writing_id" class="input">
                    <option value="">—</option>
                    @foreach ($writings as $w)
                        <option value="{{ $w->id }}" @selected((int) old('writing_id', $photo->writing_id) === $w->id)>
                            {{ $w->getTranslation('title', 'tr', false) }}
                        </option>
                    @endforeach
                </select>
                @error('writing_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="flex items-center justify-between gap-2 pt-4">
            <a href="{{ route('admin.photos.index') }}" class="btn btn--ghost btn--sm">← Listeye dön</a>
            <button type="submit" class="btn btn--accent">{{ $isNew ? 'Fotoğrafı ekle' : 'Kaydet' }}</button>
        </div>
    </div>

    {{-- Sidebar --}}
    <aside class="flex flex-col gap-6">
        <div class="admin-card">
            <h2 class="admin-card-title">Yayın durumu</h2>
            <div class="flex flex-col gap-3 text-sm">
                <label class="inline-flex items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" class="input-checkbox"
                           @checked(old('is_published', $photo->is_published))>
                    Yayında
                </label>
                <label class="inline-flex items-center gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="input-checkbox"
                           @checked(old('is_featured', $photo->is_featured))>
                    Öne çıkan
                </label>
                <label class="inline-flex items-center gap-3">
                    <input type="hidden" name="hero_eligible" value="0">
                    <input type="checkbox" name="hero_eligible" value="1" class="input-checkbox"
                           @checked(old('hero_eligible', $photo->hero_eligible))>
                    Hero adayı
                </label>
            </div>
        </div>

        <div class="admin-card">
            <h2 class="admin-card-title">Görsel</h2>
            @if ($photo->hasImage())
                <figure class="m-0 mb-3">
                    <img src="{{ $photo->imageUrl('w640') ?? $photo->imageUrl() }}"
                         alt="{{ $photo->resolvedAltText() }}"
                         class="block w-full rounded-[var(--radius-sm)]">
                </figure>
            @endif

            <div class="field">
                <label for="image" class="field-label">
                    {{ $photo->hasImage() ? 'Görseli değiştir' : 'Görsel yükle' }}
                </label>
                <input id="image" name="image" type="file"
                       accept="image/jpeg,image/png,image/webp,image/avif"
                       class="input">
                @error('image') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">JPG / PNG / WebP / AVIF. En fazla 20 MB. Otomatik 640/1280/1920/2560 WebP varyantları üretilir.</p>
            </div>
        </div>
    </aside>
</form>

@unless ($isNew)
    {{-- Separate forms for image removal + destroy (outside main form) --}}
    <div class="admin-card mt-6 flex flex-wrap gap-3 items-center justify-between">
        <p class="text-sm text-[var(--color-ink-muted)]">Tehlikeli bölge — geri alınabilir (çöp 30 gün).</p>
        <div class="flex items-center gap-2">
            @if ($photo->hasImage())
                <form method="POST" action="{{ route('admin.photos.image.remove', $photo) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--ghost btn--sm"
                            onclick="return confirm('Görsel silinsin mi?')">Görseli kaldır</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost btn--sm text-[var(--color-danger)]"
                        onclick="return confirm('Fotoğrafı çöpe al?')">Fotoğrafı sil</button>
            </form>
        </div>
    </div>
@endunless
