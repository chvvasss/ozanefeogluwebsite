<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicationRequest extends FormRequest
{
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
        $publicationId = $this->route('publication')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('publications', 'name')->ignore($publicationId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:140',
                'regex:/^[a-z0-9][a-z0-9\-]*$/',
                Rule::unique('publications', 'slug')->ignore($publicationId),
            ],
            'url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Yayın adı zorunludur.',
            'name.unique' => 'Bu isimde bir yayın zaten kayıtlı.',
            'slug.regex' => 'Slug yalnız küçük harf, rakam ve tire içerebilir.',
            'slug.unique' => 'Bu slug başka bir yayına atanmış.',
            'url.url' => 'Geçerli bir URL girin (örn. https://ornek.com).',
            'sort_order.integer' => 'Sıra numarası tam sayı olmalı.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_order' => $this->filled('sort_order') ? (int) $this->input('sort_order') : 0,
        ]);
    }
}
