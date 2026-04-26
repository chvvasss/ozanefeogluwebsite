@extends('layouts.admin', ['title' => 'Fotoğrafı düzenle'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Arşiv</p>
        <h1 class="admin-page-title">{{ $photo->getTranslation('title', 'tr', false) ?: 'Başlıksız fotoğraf' }}</h1>
        <p class="admin-page-subtitle">
            {{ $photo->kind_label }}
            @if ($photo->captured_at)
                <span class="dateline-separator">·</span>
                <span class="tabular-nums">{{ $photo->captured_at->format('Y-m-d') }}</span>
            @endif
            @if ($photo->is_published && Route::has('visuals.show'))
                <span class="dateline-separator">·</span>
                <a href="{{ $photo->url() }}" target="_blank" rel="noopener" class="underline underline-offset-4">herkese açık &rarr;</a>
            @endif
        </p>
    </div>
</header>

@include('admin.photos._form')
@endsection
