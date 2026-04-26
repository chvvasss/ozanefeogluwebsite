@extends('layouts.admin', ['title' => 'Kullanıcı düzenle'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.users.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Kullanıcılar</a>
            &nbsp;·&nbsp; düzenle
        </p>
        <h1 class="admin-page-title">{{ $user->name }}</h1>
        <p class="admin-page-subtitle font-mono text-xs">{{ $user->email }}</p>
    </div>
</header>

@include('admin.users._form')

@endsection
