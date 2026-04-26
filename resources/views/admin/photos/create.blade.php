@extends('layouts.admin', ['title' => 'Yeni fotoğraf'])

@section('content')
<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Arşiv</p>
        <h1 class="admin-page-title">Yeni fotoğraf</h1>
        <p class="admin-page-subtitle">Görseli yükle, metin + künye gir, yayınlamaya hazır olduğunda "Yayında" işaretle.</p>
    </div>
</header>

@include('admin.photos._form')
@endsection
