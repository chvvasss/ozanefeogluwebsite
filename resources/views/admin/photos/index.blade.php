@extends('layouts.admin', ['title' => 'Fotoğraflar'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Arşiv · görsel</p>
        <h1 class="admin-page-title">Fotoğraflar</h1>
        <p class="admin-page-subtitle">{{ $photos->total() }} kayıt</p>
    </div>
    @can('create', App\Models\Photo::class)
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn--ghost" onclick="document.getElementById('bulk-upload-panel').classList.toggle('hidden')">
                ⇪ Toplu yükle
            </button>
            <a href="{{ route('admin.photos.create') }}" class="btn btn--accent">+ Yeni fotoğraf</a>
        </div>
    @endcan
</header>

@can('create', App\Models\Photo::class)
    <div id="bulk-upload-panel" class="admin-card mb-6 hidden">
        <h2 class="admin-card-title">Toplu fotoğraf yükle</h2>
        <form method="POST" action="{{ route('admin.photos.bulk-upload') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            <div class="field">
                <label for="bulk-images" class="field-label">Fotoğraflar (en fazla 20, her biri 20 MB)</label>
                <input id="bulk-images" name="images[]" type="file" multiple
                       accept="image/jpeg,image/png,image/webp,image/avif"
                       class="input"
                       required>
                @error('images') <p class="field-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">
                    Her dosya için taslak kayıt oluşturulur (isim dosya adından türetilir, varsayılan künye site ayarından).
                    Ardından tek tek düzenleyip yayınla.
                </p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit" class="btn btn--accent">Yükle</button>
            </div>
        </form>
    </div>
@endcan

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.photos.index') }}"
      class="admin-card admin-filter-bar flex flex-wrap items-end gap-4 mb-6">
    <div class="field flex-1 min-w-[12rem]">
        <label for="q" class="field-label">Ara</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}"
               placeholder="başlık, slug veya konum…" class="input">
    </div>
    <div class="field w-40">
        <label for="status" class="field-label">Durum</label>
        <select id="status" name="status" class="input">
            <option value="">hepsi</option>
            <option value="published" @selected(($filters['status'] ?? '') === 'published')>yayında</option>
            <option value="draft"     @selected(($filters['status'] ?? '') === 'draft')>taslak</option>
            <option value="hero"      @selected(($filters['status'] ?? '') === 'hero')>hero adayı</option>
        </select>
    </div>
    <div class="field w-40">
        <label for="kind" class="field-label">Tür</label>
        <select id="kind" name="kind" class="input">
            <option value="">hepsi</option>
            @foreach (App\Models\Photo::KINDS as $k)
                <option value="{{ $k }}" @selected(($filters['kind'] ?? '') === $k)>{{ $k }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--sm">Süz</button>
        <a href="{{ route('admin.photos.index') }}" class="btn btn--ghost btn--sm">Temizle</a>
    </div>
</form>

@if ($photos->isEmpty())
    <div class="admin-card text-center py-16">
        <p class="display-fraunces text-2xl mb-2">Henüz fotoğraf yok.</p>
        <p class="text-sm text-[var(--color-ink-muted)] mb-6">
            İlk fotoğrafını eklemek için sağ üstteki "Yeni fotoğraf" düğmesini kullan.
        </p>
        @can('create', App\Models\Photo::class)
            <a href="{{ route('admin.photos.create') }}" class="btn btn--accent">+ Yeni fotoğraf</a>
        @endcan
    </div>
@else
    <div x-data="{
            selected: [],
            toggle(id) {
                const i = this.selected.indexOf(id);
                if (i === -1) { this.selected.push(id); } else { this.selected.splice(i, 1); }
            },
            selectAll(ids, checked) {
                this.selected = checked ? [...ids] : [];
            },
        }">
    @php
        $pagePhotoIds = $photos->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    @endphp

    <div class="flex items-center gap-3 mb-3 text-sm text-[var(--color-ink-muted)]">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   class="input-checkbox"
                   @change="selectAll(@js($pagePhotoIds), $event.target.checked)"
                   :checked="selected.length === {{ count($pagePhotoIds) }} && selected.length > 0">
            <span>{{ __('Tümünü seç') }}</span>
        </label>
        <span x-show="selected.length > 0" x-cloak x-text="`· ${selected.length} {{ __('seçili') }}`"></span>
    </div>

    <div class="photo-grid">
        @foreach ($photos as $photo)
            @php $isTrashed = method_exists($photo, 'trashed') && $photo->trashed(); @endphp
            <article class="photo-card @if ($isTrashed) opacity-60 @endif" style="position: relative;">
                <label class="inline-flex items-center gap-1"
                       style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 2; background: rgba(255,255,255,0.85); padding: 0.15rem 0.35rem; border-radius: 0.25rem;">
                    <input type="checkbox"
                           class="input-checkbox"
                           value="{{ $photo->id }}"
                           aria-label="{{ __('Fotoğrafı seç') }}"
                           @change="toggle({{ $photo->id }})"
                           :checked="selected.includes({{ $photo->id }})">
                </label>
                <a href="{{ route('admin.photos.edit', $photo) }}" class="photo-card-thumb">
                    @if ($photo->hasImage())
                        <img src="{{ $photo->imageUrl('w640') ?? $photo->imageUrl() }}"
                             alt="{{ $photo->resolvedAltText() }}"
                             loading="lazy">
                    @else
                        <span class="photo-card-placeholder" aria-hidden="true">◨</span>
                    @endif
                </a>
                <div class="photo-card-meta">
                    <h3 class="photo-card-title">
                        <a href="{{ route('admin.photos.edit', $photo) }}">{{ $photo->getTranslationWithFallback('title') ?: '—' }}</a>
                    </h3>
                    <p class="photo-card-detail">
                        <span>{{ $photo->kind_label }}</span>
                        @if ($photo->location)
                            <span class="dateline-separator">·</span>
                            <span>{{ $photo->location }}</span>
                        @endif
                        @if ($photo->captured_at)
                            <span class="dateline-separator">·</span>
                            <span class="tabular-nums">{{ $photo->captured_at->format('Y-m-d') }}</span>
                        @endif
                    </p>
                    <p class="photo-card-flags">
                        @if ($photo->is_published)
                            <span class="pill pill--published">yayında</span>
                        @else
                            <span class="pill pill--draft">taslak</span>
                        @endif
                        @if ($photo->is_featured)
                            <span class="pill pill--accent">öne çıkan</span>
                        @endif
                        @if ($photo->hero_eligible)
                            <span class="pill pill--hero">hero</span>
                        @endif
                        @if ($isTrashed)
                            <span class="pill pill--trash">çöpte</span>
                        @endif
                    </p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $photos->links() }}</div>

    @include('admin.photos._bulk-bar')
    </div>
@endif

@endsection
