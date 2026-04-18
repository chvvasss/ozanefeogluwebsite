@extends('layouts.admin', ['title' => ($writing->getTranslation('title', 'tr', false) ?: 'Yazı') . ' düzenle'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.writings.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Yazılar</a>
            &nbsp;·&nbsp; düzenle
        </p>
        <h1 class="admin-page-title" style="font-size: var(--text-2xl);">
            {{ $writing->getTranslation('title', 'tr', false) ?: '(başlıksız)' }}
        </h1>
        <p class="admin-page-subtitle">
            /{{ $writing->getTranslation('slug', 'tr', false) }}
            · son güncelleme {{ $writing->updated_at?->diffForHumans() }}
        </p>
    </div>
</header>

@include('admin.writings._form')

@endsection
