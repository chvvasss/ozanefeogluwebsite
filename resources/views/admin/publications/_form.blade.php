@php
    $isUpdate = $publication->exists;
    $action = $isUpdate
        ? route('admin.publications.update', $publication)
        : route('admin.publications.store');
    $method = $isUpdate ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
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

    <div class="grid gap-6 lg:grid-cols-[2fr_1fr] items-start">
        <div class="space-y-6">
            <div class="admin-card">
                <div class="field mb-5">
                    <label for="name" class="field-label">Ad</label>
                    <input id="name" name="name" type="text" required maxlength="120"
                           value="{{ old('name', $publication->name) }}"
                           class="input"
                           style="font-family: var(--font-display); font-size: 1.25rem; line-height: 1.2;"
                           placeholder="Birikim, Express, Gazete Duvar…">
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-hint">Yayın organının resmi adı. Yazı editöründe bu isimle seçilecek.</p>
                </div>

                <div class="field">
                    <label for="slug" class="field-label">Slug (opsiyonel)</label>
                    <input id="slug" name="slug" type="text" maxlength="140"
                           value="{{ old('slug', $publication->slug) }}"
                           pattern="^[a-z0-9][a-z0-9\-]*$"
                           class="input font-mono text-sm"
                           placeholder="(boş bırakılırsa addan üretilir)">
                    @error('slug') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-hint">Küçük harf · rakam · tire. Boş bırakılırsa addan otomatik türetilir.</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="field">
                    <label for="url" class="field-label">Web sitesi</label>
                    <input id="url" name="url" type="url" maxlength="255"
                           value="{{ old('url', $publication->url) }}"
                           class="input"
                           placeholder="https://orneksite.com">
                    @error('url') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-hint">Opsiyonel. Yayın organının ana sayfası.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="admin-card">
                <p class="admin-card-title">Sıra</p>
                <div class="field">
                    <label for="sort_order" class="field-label">Sıralama</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" max="9999"
                           value="{{ old('sort_order', $publication->sort_order ?? 0) }}"
                           class="input max-w-[8rem]">
                    @error('sort_order') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-hint">Düşük sayı önce görünür. Eşitse ada göre alfabetik.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.publications.index') }}" class="btn btn--ghost btn--sm">İptal</a>
                <button type="submit" class="btn btn--accent">
                    {{ $isUpdate ? 'Güncelle' : 'Oluştur' }}
                </button>
            </div>
        </div>
    </div>
</form>
