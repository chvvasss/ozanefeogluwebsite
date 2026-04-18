@extends('layouts.admin', ['title' => 'Yeni yazı'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.writings.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Yazılar</a>
            &nbsp;·&nbsp; yeni
        </p>
        <h1 class="admin-page-title">Yeni yazı</h1>
        <p class="admin-page-subtitle">Başlık ve gövde ile başla; geri kalanı sonra da doldurabilirsin.</p>
    </div>
</header>

@include('admin.writings._form')

@endsection
