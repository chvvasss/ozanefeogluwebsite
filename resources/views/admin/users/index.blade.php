@extends('layouts.admin', ['title' => 'Kullanıcılar'])

@section('content')

@php
    $roleLabels = [
        'super-admin' => 'süper-admin',
        'admin'       => 'admin',
        'editor'      => 'editör',
        'contributor' => 'katkıcı',
        'viewer'      => 'okur',
    ];
@endphp

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">Hesaplar · erişim</p>
        <h1 class="admin-page-title">Kullanıcılar</h1>
        <p class="admin-page-subtitle">{{ $users->total() }} kayıt</p>
    </div>
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="btn btn--accent">+ Yeni kullanıcı</a>
    @endcan
</header>

@if (session('status'))
    <div class="flash flash--success mb-4">{{ session('status') }}</div>
@endif

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.users.index') }}"
      class="admin-card admin-filter-bar flex flex-wrap items-end gap-4 mb-6">
    <div class="field flex-1 min-w-[12rem]">
        <label for="q" class="field-label">Ara</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}"
               placeholder="isim veya e-posta…" class="input">
    </div>
    <div class="field w-48">
        <label for="role" class="field-label">Rol</label>
        <select id="role" name="role" class="input">
            <option value="">hepsi</option>
            @foreach ($roles as $roleName)
                <option value="{{ $roleName }}" @selected(($filters['role'] ?? '') === $roleName)>
                    {{ $roleLabels[$roleName] ?? $roleName }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--sm">Süz</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost btn--sm">Temizle</a>
    </div>
</form>

@if ($users->isEmpty())
    <div class="admin-card text-center py-16">
        <p class="display-fraunces text-2xl mb-2">Kayıtlı kullanıcı yok.</p>
        <p class="text-sm text-[var(--color-ink-muted)] mb-6">
            İlk kullanıcıyı oluşturmak için sağ üstteki "Yeni kullanıcı" düğmesini kullan.
        </p>
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}" class="btn btn--accent">+ Yeni kullanıcı</a>
        @endcan
    </div>
@else
    <div class="admin-card p-0 overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>İsim</th>
                    <th>E-posta</th>
                    <th class="w-32">Rol</th>
                    <th class="w-36">Son giriş</th>
                    <th class="text-right w-48">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $row)
                    @php $rowRole = (string) $row->roles->pluck('name')->first(); @endphp
                    <tr>
                        <td>
                            @can('update', $row)
                                <a href="{{ route('admin.users.edit', $row) }}"
                                   class="no-underline text-[var(--color-ink)] hover:text-[var(--color-accent)] font-medium">
                                    {{ $row->name ?: '(isimsiz)' }}
                                </a>
                            @else
                                <span class="font-medium">{{ $row->name ?: '(isimsiz)' }}</span>
                            @endcan
                        </td>
                        <td class="font-mono text-xs text-[var(--color-ink-muted)]">{{ $row->email }}</td>
                        <td>
                            <span class="pill">{{ $roleLabels[$rowRole] ?? $rowRole ?: '—' }}</span>
                        </td>
                        <td class="font-mono text-xs tabular-nums text-[var(--color-ink-muted)]">
                            {{ optional($row->last_login_at)->format('Y-m-d') ?? '—' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                @can('update', $row)
                                    <a href="{{ route('admin.users.edit', $row) }}"
                                       class="btn btn--ghost btn--sm">Düzenle</a>
                                @endcan

                                @can('delete', $row)
                                    <form method="POST" action="{{ route('admin.users.destroy', $row) }}" class="inline"
                                          onsubmit="return confirm('Bu kullanıcı silinsin?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--ghost btn--sm" title="Sil">
                                            <span class="text-[var(--color-danger)]">×</span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endif

@endsection
