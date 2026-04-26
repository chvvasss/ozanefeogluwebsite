@extends('layouts.admin', ['title' => 'Yeni kullanıcı'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.users.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Kullanıcılar</a>
            &nbsp;·&nbsp; yeni
        </p>
        <h1 class="admin-page-title">Yeni kullanıcı</h1>
        <p class="admin-page-subtitle">Yeni bir hesap oluştur ve uygun rolü ata.</p>
    </div>
</header>

@include('admin.users._form')

@endsection
