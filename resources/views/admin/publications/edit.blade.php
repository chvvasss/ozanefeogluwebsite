@extends('layouts.admin', ['title' => ($publication->name ?: 'Yayın') . ' düzenle'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.publications.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Yayınlar</a>
            &nbsp;·&nbsp; düzenle
        </p>
        <h1 class="admin-page-title" style="font-size: var(--text-2xl);">{{ $publication->name }}</h1>
        <p class="admin-page-subtitle font-mono text-xs">/{{ $publication->slug }}</p>
    </div>
</header>

@include('admin.publications._form', ['publication' => $publication])

@endsection
