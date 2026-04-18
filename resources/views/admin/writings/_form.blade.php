@php
    /** @var \App\Models\Writing $writing */
    $isUpdate = $writing->exists;
    $action = $isUpdate
        ? route('admin.writings.update', $writing)
        : route('admin.writings.store');
    $method = $isUpdate ? 'PUT' : 'POST';
    $titleTr = old('title_tr', $writing->getTranslation('title', 'tr', false));
    $slugTr = old('slug_tr', $writing->getTranslation('slug', 'tr', false));
    $excerptTr = old('excerpt_tr', $writing->getTranslation('excerpt', 'tr', false));
    $bodyTr = old('body_tr', $writing->getTranslation('body', 'tr', false));
    $metaTitleTr = old('meta_title_tr', $writing->getTranslation('meta_title', 'tr', false));
    $metaDescTr = old('meta_desc_tr', $writing->getTranslation('meta_description', 'tr', false));
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method($method)

    @if ($errors->any())
        <div class="flash flash--danger">
            <ul class="list-disc pl-4 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[2.2fr_1fr] items-start">

        {{-- ---------------- Left column ---------------- --}}
        <div class="space-y-6">

            <div class="admin-card">
                <div class="field mb-5">
                    <label for="title_tr" class="field-label">Başlık</label>
                    <input id="title_tr" name="title_tr" type="text" required
                           value="{{ $titleTr }}" class="input text-lg"
                           style="font-family: var(--font-display); font-size: 1.5rem; line-height: 1.2;">
                    @error('title_tr') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="slug_tr" class="field-label">Slug</label>
                    <input id="slug_tr" name="slug_tr" type="text" value="{{ $slugTr }}"
                           pattern="^[a-z0-9][a-z0-9\-]*$"
                           placeholder="(boş bırak → başlıktan otomatik)"
                           class="input font-mono text-sm">
                    <p class="field-hint">Küçük harf · rakam · tire. Değiştirirsen eski URL 301 redirect'e düşer.</p>
                    @error('slug_tr') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="admin-card">
                <p class="admin-card-title">Özet</p>
                <textarea id="excerpt_tr" name="excerpt_tr" rows="3" maxlength="500"
                          class="input resize-y"
                          placeholder="Kart ve SEO için 1-2 cümle.">{{ $excerptTr }}</textarea>
                @error('excerpt_tr') <p class="field-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="admin-card p-0 overflow-hidden">
                <div class="p-5 pb-3 border-b border-[var(--color-rule)]">
                    <p class="admin-card-title !mb-0">Gövde</p>
                </div>
                <div x-data="tiptapEditor(@js($bodyTr ?? ''))" x-init="init()" class="tiptap-wrap">
                    <div class="tiptap-toolbar" role="toolbar" aria-label="Biçim araçları">
                        <button type="button" @click="chain('toggleHeading', { level: 2 })" :class="active('heading', { level: 2 }) && 'is-active'" title="Başlık 2">H2</button>
                        <button type="button" @click="chain('toggleHeading', { level: 3 })" :class="active('heading', { level: 3 }) && 'is-active'" title="Başlık 3">H3</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('toggleBold')" :class="active('bold') && 'is-active'" title="Kalın"><strong>B</strong></button>
                        <button type="button" @click="chain('toggleItalic')" :class="active('italic') && 'is-active'" title="İtalik"><em>I</em></button>
                        <button type="button" @click="chain('toggleStrike')" :class="active('strike') && 'is-active'" title="Üstü çizili">S</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('toggleBulletList')" :class="active('bulletList') && 'is-active'" title="Madde işaretli">•</button>
                        <button type="button" @click="chain('toggleOrderedList')" :class="active('orderedList') && 'is-active'" title="Sıralı">1.</button>
                        <button type="button" @click="chain('toggleBlockquote')" :class="active('blockquote') && 'is-active'" title="Alıntı">"</button>
                        <button type="button" @click="chain('toggleCodeBlock')" :class="active('codeBlock') && 'is-active'" title="Kod bloğu">&lt;/&gt;</button>
                        <button type="button" @click="chain('setHorizontalRule')" title="Ayraç">—</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="setLink()" :class="active('link') && 'is-active'" title="Link">🔗</button>
                        <button type="button" @click="unsetLink()" title="Link kaldır">⊘</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('undo')" title="Geri al">↶</button>
                        <button type="button" @click="chain('redo')" title="İleri al">↷</button>
                    </div>
                    <div x-ref="editor" class="tiptap-content"></div>
                    <textarea name="body_tr" x-ref="hidden" class="sr-only" required>{{ $bodyTr }}</textarea>
                </div>
                @error('body_tr') <p class="field-error m-4">{{ $message }}</p> @enderror
            </div>

            {{-- SEO collapse --}}
            <details class="admin-card">
                <summary class="admin-card-title cursor-pointer select-none flex items-center gap-2">
                    <span>SEO meta</span>
                    <span class="text-[var(--color-ink-subtle)] text-xs font-normal normal-case tracking-normal">(opsiyonel override)</span>
                </summary>
                <div class="pt-4 space-y-4">
                    <div class="field">
                        <label for="meta_title_tr" class="field-label">Meta başlık</label>
                        <input id="meta_title_tr" name="meta_title_tr" type="text" maxlength="255"
                               value="{{ $metaTitleTr }}" class="input">
                    </div>
                    <div class="field">
                        <label for="meta_desc_tr" class="field-label">Meta açıklama</label>
                        <textarea id="meta_desc_tr" name="meta_desc_tr" rows="2" maxlength="320"
                                  class="input resize-y">{{ $metaDescTr }}</textarea>
                    </div>
                    <div class="field">
                        <label for="canonical_url" class="field-label">Canonical URL</label>
                        <input id="canonical_url" name="canonical_url" type="url" maxlength="255"
                               value="{{ old('canonical_url', $writing->canonical_url) }}"
                               placeholder="https://other-site.com/original-url" class="input font-mono text-sm">
                    </div>
                </div>
            </details>
        </div>

        {{-- ---------------- Right column (meta) ---------------- --}}
        <div class="space-y-6">
            <div class="admin-card">
                <p class="admin-card-title">Durum &amp; yayım</p>

                <div class="field mb-4">
                    <label for="status" class="field-label">Durum</label>
                    <select id="status" name="status" class="input">
                        <option value="draft"     @selected(old('status', $writing->status) === 'draft')>taslak</option>
                        <option value="scheduled" @selected(old('status', $writing->status) === 'scheduled')>planlı</option>
                        <option value="published" @selected(old('status', $writing->status) === 'published')>yayında</option>
                    </select>
                </div>

                <div class="field mb-4">
                    <label for="published_at" class="field-label">Yayım zamanı</label>
                    <input id="published_at" name="published_at" type="datetime-local"
                           value="{{ old('published_at', optional($writing->published_at)->format('Y-m-d\TH:i')) }}"
                           class="input font-mono text-sm">
                    <p class="field-hint">Planlı ise gelecekte, yayındaysa geçmişte/şimdi.</p>
                </div>

                <label class="flex items-center gap-2 text-sm mt-3 cursor-pointer select-none">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                           class="accent-[var(--color-accent)]"
                           @checked(old('is_featured', $writing->is_featured))>
                    <span>Öne çıkar (hero kart)</span>
                </label>
            </div>

            <div class="admin-card">
                <p class="admin-card-title">Tür</p>
                <div class="field">
                    <label for="kind" class="field-label">Kategori</label>
                    <select id="kind" name="kind" class="input">
                        <option value="saha_yazisi" @selected(old('kind', $writing->kind) === 'saha_yazisi')>saha yazısı</option>
                        <option value="roportaj"    @selected(old('kind', $writing->kind) === 'roportaj')>röportaj</option>
                        <option value="deneme"      @selected(old('kind', $writing->kind) === 'deneme')>deneme</option>
                        <option value="not"         @selected(old('kind', $writing->kind) === 'not')>not</option>
                    </select>
                </div>
                <div class="field mt-4">
                    <label for="location" class="field-label">Konum</label>
                    <input id="location" name="location" type="text" maxlength="120"
                           value="{{ old('location', $writing->location) }}"
                           placeholder="Gazze, İstanbul..." class="input">
                </div>
            </div>

            <div class="admin-card">
                <p class="admin-card-title">Kapak</p>
                @if ($isUpdate && method_exists($writing, 'getFirstMedia') && $writing->getFirstMedia('cover'))
                    <figure class="mb-3 relative overflow-hidden rounded-[var(--radius-sm)] aspect-[4/3]">
                        <img src="{{ $writing->coverUrl('w640') ?? $writing->coverUrl() }}"
                             alt="" class="w-full h-full object-cover">
                    </figure>
                    <form method="POST" action="{{ route('admin.writings.cover.remove', $writing) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--ghost btn--sm text-[var(--color-danger)]"
                                onclick="return confirm('Kapak kaldırılsın?')">
                            Kapağı kaldır
                        </button>
                    </form>
                @else
                    <div class="field">
                        <input id="cover" name="cover" type="file" accept="image/*"
                               class="input text-sm">
                        <p class="field-hint">JPG · PNG · WebP · AVIF — max 8 MB</p>
                    </div>
                @endif

                <hr class="my-5 border-[var(--color-rule)]">

                <p class="text-xs text-[var(--color-ink-subtle)] mb-3 uppercase tracking-[0.15em]">Placeholder renk (kapak yoksa)</p>
                <div class="grid grid-cols-2 gap-3"
                     x-data="{ a: Number('{{ old('cover_hue_a', $writing->cover_hue_a ?? 24) }}'), b: Number('{{ old('cover_hue_b', $writing->cover_hue_b ?? 200) }}') }">
                    <label class="field">
                        <span class="field-label">Ton A</span>
                        <input type="range" name="cover_hue_a" min="0" max="255" x-model.number="a" class="w-full">
                        <span class="text-xs font-mono text-[var(--color-ink-subtle)]" x-text="a"></span>
                    </label>
                    <label class="field">
                        <span class="field-label">Ton B</span>
                        <input type="range" name="cover_hue_b" min="0" max="255" x-model.number="b" class="w-full">
                        <span class="text-xs font-mono text-[var(--color-ink-subtle)]" x-text="b"></span>
                    </label>
                    <div class="col-span-2 aspect-[4/3] rounded-[var(--radius-sm)] overflow-hidden cover-placeholder"
                         :style="`--hue-a: ${a}; --hue-b: ${b};`"></div>
                </div>
            </div>

            @if (! empty($publications ?? []))
                <div class="admin-card">
                    <p class="admin-card-title">Yayınlar (byline)</p>
                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                        @foreach ($publications as $pub)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="publication_ids[]" value="{{ $pub->id }}"
                                       class="accent-[var(--color-accent)]"
                                       @checked(in_array($pub->id, old('publication_ids', $selectedPubs ?? []), true))>
                                <span>{{ $pub->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end gap-2 sticky bottom-4">
                @if ($isUpdate)
                    <a href="{{ $writing->url() }}" target="_blank" rel="noopener"
                       class="btn btn--ghost btn--sm">Public'te aç ↗</a>
                @endif
                <a href="{{ route('admin.writings.index') }}" class="btn btn--ghost btn--sm">İptal</a>
                <button type="submit" class="btn btn--accent">
                    {{ $isUpdate ? 'Güncelle' : 'Oluştur' }}
                </button>
            </div>
        </div>
    </div>
</form>
