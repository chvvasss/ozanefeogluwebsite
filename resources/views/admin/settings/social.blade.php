@extends('layouts.admin', ['title' => 'Sosyal · Ayarlar'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Site</p>
        <h1 class="admin-page-title">Ayarlar</h1>
        <p class="admin-page-subtitle">Herkese açık sosyal hesap bağlantıları. Boş bırakılan alanlar footer ve iletişim sayfasında gizlenir.</p>
    </div>
</header>

@include('admin.settings._tabs')

<form method="POST" action="{{ route('admin.settings.update', ['group' => $group]) }}" class="admin-card flex flex-col gap-6 mt-6">
    @csrf
    @method('PUT')

    @php
        $fields = [
            'mastodon_url'  => ['Mastodon', 'https://mastodon.social/@...'],
            'bluesky_url'   => ['Bluesky',  'https://bsky.app/profile/...'],
            'x_url'         => ['X',        'https://x.com/...'],
            'instagram_url' => ['Instagram', 'https://instagram.com/...'],
            'linkedin_url'  => ['LinkedIn', 'https://linkedin.com/in/...'],
            'github_url'    => ['GitHub',   'https://github.com/...'],
        ];
    @endphp

    <section class="flex flex-col gap-5">
        <h2 class="admin-card-title">Hesaplar</h2>

        @foreach ($fields as $field => [$label, $placeholder])
            @php $key = 'social.' . $field; @endphp
            <div class="field">
                <label for="s_{{ $field }}" class="field-label">{{ $label }}</label>
                <input id="s_{{ $field }}" name="social[{{ $field }}]" type="url" maxlength="200"
                       class="input"
                       placeholder="{{ $placeholder }}"
                       value="{{ old($key, $values[$key] ?? '') }}">
                @error($key) <p class="field-error">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </section>

    <div class="flex items-center justify-end gap-2 pt-4">
        <button type="submit" class="btn btn--accent">Sosyal bağlantıları kaydet</button>
    </div>
</form>
@endsection
