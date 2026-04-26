<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'title_tr' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9][a-z0-9\-]*$/', Rule::unique('pages', 'slug')->ignore($pageId)],
            'template' => ['required', Rule::in(Page::TEMPLATES)],
            'intro_tr' => ['nullable', 'string', 'max:500'],
            'body_tr' => ['nullable', 'string', 'max:200000'],
            'meta_title_tr' => ['nullable', 'string', 'max:255'],
            'meta_desc_tr' => ['nullable', 'string', 'max:320'],
            'extras_json' => ['nullable', 'string', 'max:20000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title_tr.required' => 'Başlık zorunludur.',
            'slug.regex' => 'Slug yalnız küçük harf, rakam ve tire içerebilir.',
            'slug.unique' => 'Bu slug başka bir sayfada kullanılıyor.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => (bool) $this->boolean('is_published'),
        ]);
    }

    /**
     * Decode the extras JSON textarea to an array (or null).
     *
     * @return array<string, mixed>|null
     */
    public function extras(): ?array
    {
        $raw = trim((string) ($this->validated()['extras_json'] ?? ''));
        if ($raw === '') {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }
}
