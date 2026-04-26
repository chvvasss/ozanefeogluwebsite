@extends('layouts.admin', ['title' => 'Hero · Ayarlar'])

@section('content')
@php
    /** @var string $group */
    /** @var array<int, string> $groups */
    /** @var array<string, mixed> $values */
    /** @var array<int, string> $heroModes */
    /** @var \Illuminate\Support\Collection $heroCandidates */

    $modeLabels = [
        'featured_photo' => ['Öne çıkan fotoğraf', 'Seçilmiş yazının kapağı büyük hero olur. Kapak yoksa otomatik tipografiye düşer.'],
        'rotation'       => ['Rotasyon', 'Hero adayı yazılar (hero_eligible işaretli) arasında her gün sırayla biri gösterilir.'],
        'typographic'    => ['Tipografik', 'Fotoğrafsız; sadece editoryal plate + isim + roller.'],
        'portrait'       => ['Portre', 'Klasik portre + plate düzeni. (Öneri: sadece kısa dönem.)'],
    ];
@endphp

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Anasayfa hero davranışı. Kapak fotoğrafları hazır olana kadar tipografik mod önerilir.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Hero modu</h2>

        <div class="flex flex-col gap-3">
            @foreach ($heroModes as $mode)
                @php
                    [$label, $desc] = $modeLabels[$mode] ?? [ucfirst($mode), ''];
                    $current = old('hero.mode', $values['hero.mode'] ?? 'featured_photo');
                @endphp
                <label class="admin-radio">
                    <input type="radio" name="hero[mode]" value="{{ $mode }}" @checked($current === $mode)>
                    <span class="admin-radio-title">{{ $label }}</span>
                    <span class="admin-radio-desc">{{ $desc }}</span>
                </label>
            @endforeach
        </div>
        @error('hero.mode') <p class="field-error">{{ $message }}</p> @enderror
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Öne çıkan yazı</h2>

        <div class="field">
            <label for="hero_featured" class="field-label">Öne çıkan yazı (opsiyonel)</label>
            <select id="hero_featured" name="hero[featured_writing_id]" class="input">
                <option value="">— Otomatik (en son hero_eligible) —</option>
                @foreach ($heroCandidates as $w)
                    <option value="{{ $w->id }}"
                            @selected((int) old('hero.featured_writing_id', $values['hero.featured_writing_id'] ?? 0) === $w->id)>
                        {{ $w->title }}
                        @if ($w->published_at)
                            · {{ $w->published_at->format('Y-m-d') }}
                        @endif
                    </option>
                @endforeach
            </select>
            @error('hero.featured_writing_id') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Boş bırakırsan sistem en yeni <em>hero_eligible</em> yazıyı otomatik seçer. Listede yalnızca hero adayı olarak işaretli yazılar görünür (Yazılar &rarr; düzenle &rarr; <em>Hero adayı</em> kutusu).</p>
        </div>

        @if ($heroCandidates->isEmpty())
            <p class="flash flash--warning">
                Şu anda hero adayı olarak işaretli yayımlanmış yazı yok. Yazı düzenleme ekranında <strong>Hero adayı</strong> kutusunu işaretle, sonra bu listeyi doldurabilirsin.
            </p>
        @endif
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">Hero ayarlarını kaydet</button>
    </div>
</form>
@endsection
