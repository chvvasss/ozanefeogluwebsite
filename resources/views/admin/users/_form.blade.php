@php
    /** @var \App\Models\User $user */
    /** @var string $currentRole */
    /** @var array<int, string> $availableRoles */
    $isUpdate = $user->exists;
    $action   = $isUpdate
        ? route('admin.users.update', $user)
        : route('admin.users.store');
    $method   = $isUpdate ? 'PUT' : 'POST';

    $roleLabels = [
        'super-admin' => 'süper-admin',
        'admin'       => 'admin',
        'editor'      => 'editör',
        'contributor' => 'katkıcı',
        'viewer'      => 'okur',
    ];
@endphp

<form method="POST" action="{{ $action }}" class="admin-card flex flex-col gap-5 max-w-2xl">
    @csrf
    @method($method)

    @if ($errors->any())
        <div class="flash flash--danger">
            <ul class="list-disc pl-4 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="field">
        <label for="name" class="field-label">İsim</label>
        <input id="name" name="name" type="text" required
               value="{{ old('name', $user->name) }}" class="input">
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="email" class="field-label">E-posta</label>
        <input id="email" name="email" type="email" required
               value="{{ old('email', $user->email) }}" class="input">
        @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="password" class="field-label">
            Şifre
            @if ($isUpdate)
                <span class="text-xs text-[var(--color-ink-subtle)] font-normal normal-case tracking-normal">
                    (değiştirmek istemiyorsan boş bırak)
                </span>
            @endif
        </label>
        <input id="password" name="password" type="password"
               autocomplete="new-password"
               {{ $isUpdate ? '' : 'required' }}
               class="input">
        <p class="field-hint">En az 12 karakter; büyük-küçük harf ve rakam içermeli.</p>
        @error('password') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="password_confirmation" class="field-label">Şifre (tekrar)</label>
        <input id="password_confirmation" name="password_confirmation" type="password"
               autocomplete="new-password"
               {{ $isUpdate ? '' : 'required' }}
               class="input">
    </div>

    <div class="field">
        <label for="role" class="field-label">Rol</label>
        <select id="role" name="role" class="input" required>
            @foreach ($availableRoles as $roleName)
                <option value="{{ $roleName }}"
                    @selected(old('role', $currentRole) === $roleName)>
                    {{ $roleLabels[$roleName] ?? $roleName }}
                </option>
            @endforeach
        </select>
        @error('role') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-2 mt-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost btn--sm">İptal</a>
        <button type="submit" class="btn btn--accent">
            {{ $isUpdate ? 'Güncelle' : 'Oluştur' }}
        </button>
    </div>
</form>
