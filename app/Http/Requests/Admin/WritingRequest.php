<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Writing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WritingRequest extends FormRequest
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
        $writingId = $this->route('writing')?->id;

        return [
            'title_tr' => ['required', 'string', 'max:255'],
            'slug_tr' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9][a-z0-9\-]*$/'],
            'kind' => ['required', Rule::in(Writing::KINDS)],
            'status' => ['required', Rule::in(Writing::STATUSES)],
            'published_at' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:120'],
            'excerpt_tr' => ['nullable', 'string', 'max:500'],
            'body_tr' => ['required', 'string', 'max:200000'],
            'cover_hue_a' => ['required', 'integer', 'between:0,255'],
            'cover_hue_b' => ['required', 'integer', 'between:0,255'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title_tr' => ['nullable', 'string', 'max:255'],
            'meta_desc_tr' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'publication_ids' => ['nullable', 'array'],
            'publication_ids.*' => ['integer', 'exists:publications,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title_tr.required' => 'Başlık zorunludur.',
            'body_tr.required' => 'Gövde metni boş olamaz.',
            'slug_tr.regex' => 'Slug yalnız küçük harf, rakam ve tire içerebilir.',
            'kind.in' => 'Geçersiz tür.',
            'status.in' => 'Geçersiz durum.',
            'cover_hue_a.between' => 'Kapak rengi 0–255 aralığında olmalı.',
            'cover_hue_b.between' => 'Kapak rengi 0–255 aralığında olmalı.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => (bool) $this->boolean('is_featured'),
        ]);
    }
}
