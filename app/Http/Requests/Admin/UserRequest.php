<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Roles assignable via the admin UI.
     * Kept here (not in a constant on User) to avoid leaking auth concerns into the model.
     */
    public const ASSIGNABLE_ROLES = [
        'super-admin',
        'admin',
        'editor',
        'contributor',
        'viewer',
    ];

    public function authorize(): bool
    {
        // Controller-level policy check is authoritative; this stays permissive.
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var User|null $target */
        $target = $this->route('user');
        $isUpdate = $target instanceof User;
        $targetId = $isUpdate ? $target->id : null;

        $passwordRules = ['confirmed', Password::min(12)->mixedCase()->numbers()];

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($targetId),
            ],
            'password' => $isUpdate
                ? array_merge(['nullable', 'string'], $passwordRules)
                : array_merge(['required', 'string'], $passwordRules),
            'role' => ['required', 'string', Rule::in(self::ASSIGNABLE_ROLES)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim zorunludur.',
            'email.required' => 'E-posta zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi gir.',
            'email.unique' => 'Bu e-posta zaten kayıtlı.',
            'password.required' => 'Şifre zorunludur.',
            'password.min' => 'Şifre en az 12 karakter olmalı.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'role.required' => 'Rol seçimi zorunludur.',
            'role.in' => 'Geçersiz rol.',
        ];
    }
}
