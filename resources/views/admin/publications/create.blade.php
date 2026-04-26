@extends('layouts.admin', ['title' => 'Yeni yayın'])

@section('content')

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.publications.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Yayınlar</a>
            &nbsp;·&nbsp; yeni
        </p>
        <h1 class="admin-page-title" style="font-size: var(--text-2xl);">Yeni yayın</h1>
        <p class="admin-page-subtitle">Yazıların hangi organda yayınlandığını kaydedebilmek için önce yayın ekle.</p>
    </div>
</header>

@include('admin.publications._form', ['publication' => $publication])

@endsection
