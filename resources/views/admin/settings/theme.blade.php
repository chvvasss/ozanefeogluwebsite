@extends('layouts.admin', ['title' => 'Tema & Özellikler · Ayarlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Tema varsayılanı ve özellik bayrakları. Siyah/beyaz/kırmızı editörel paleti kilitli; şu an tek preset (<em>dispatch</em>) var.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Tema</h2>

        <div class="field">
            <label for="theme_preset" class="field-label">Renk preset'i</label>
            <select id="theme_preset" name="theme[preset]" required class="input max-w-[20rem]">
                <option value="dispatch" @selected(old('theme.preset', $values['theme.preset'] ?? 'dispatch') === 'dispatch')>
                    Dispatch — siyah · beyaz · kırmızı (kilitli)
                </option>
            </select>
            @error('theme.preset') <p class="field-error">{{ $message }}</p> @enderror
            <p class="field-hint">Yeni preset'ler ileride — kırmızı editoryal aksanı değiştirmek için tasarım review şart.</p>
        </div>

        <div class="field">
            <label class="field-label">Karanlık mod varsayılanı</label>
            <div class="flex flex-col gap-3">
                @foreach (App\Http\Controllers\Admin\SettingsController::DARK_MODES as $mode)
                    @php
                        $current = old('theme.dark_mode', $values['theme.dark_mode'] ?? 'light');
                        if (! in_array($current, ['light', 'dark'], true)) $current = 'light';
                        $label = match ($mode) {
                            'light'  => ['Aydınlık', 'Site varsayılan olarak aydınlık modda yüklenir.'],
                            'dark'   => ['Karanlık', 'Site varsayılan olarak karanlık modda yüklenir.'],
                        };
                    @endphp
                    <label class="admin-radio">
                        <input type="radio" name="theme[dark_mode]" value="{{ $mode }}" @checked($current === $mode)>
                        <span class="admin-radio-title">{{ $label[0] }}</span>
                        <span class="admin-radio-desc">{{ $label[1] }}</span>
                    </label>
                @endforeach
            </div>
            @error('theme.dark_mode') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </section>

    <section class="flex flex-col gap-5 pt-6 border-t border-[var(--color-rule)]">
        <h2 class="admin-card-title">Özellik bayrakları</h2>

        @php
            $flags = [
                'feed_enabled'         => ['RSS yayını',       'Ana navigasyonda /feed.xml aktifleşir.'],
                'newsletter_enabled'   => ['Bülten formu',     'Footer/İletişim sayfasında abonelik alanı görünür. (Entegrasyon D sonrası.)'],
                'search_enabled'       => ['Arama',            'Meilisearch backend devreye girer. (Entegrasyon E sonrası.)'],
                'demo_content_banner'  => ['Demo içerik uyarısı', 'Seed edilmiş demo yazılar için üstte küçük uyarı bandı.'],
            ];
        @endphp

        <div class="flex flex-col gap-3">
            @foreach ($flags as $flag => [$label, $desc])
                @php $key = 'features.' . $flag; @endphp
                <label class="admin-radio" style="grid-template-columns: auto 1fr; align-items: center;">
                    <input type="hidden" name="features[{{ $flag }}]" value="0">
                    <input type="checkbox" name="features[{{ $flag }}]" value="1"
                           class="input-checkbox"
                           @checked(old($key, $values[$key] ?? false))>
                    <span>
                        <span class="admin-radio-title">{{ $label }}</span>
                        <span class="admin-radio-desc block">{{ $desc }}</span>
                    </span>
                </label>
                @error($key) <p class="field-error">{{ $message }}</p> @enderror
            @endforeach
        </div>
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">Tema ayarlarını kaydet</button>
    </div>
</form>
@endsection
