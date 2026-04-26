<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_tr' => ['required', 'string', 'max:200'],
            'slug_tr' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9\-]+$/'],
            'caption_tr' => ['nullable', 'string', 'max:500'],
            'alt_text_tr' => ['nullable', 'string', 'max:300'],

            'kind' => ['required', 'string', 'in:'.implode(',', Photo::KINDS)],
            'credit' => ['nullable', 'string', 'max:160'],
            'source' => ['nullable', 'string', 'max:120'],
            'license' => ['nullable', 'string', 'in:'.implode(',', Photo::LICENSES)],
            'rights_notes' => ['nullable', 'string', 'max:300'],
            'location' => ['nullable', 'string', 'max:160'],
            'captured_at' => ['nullable', 'date'],

            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'hero_eligible' => ['nullable', 'boolean'],

            'writing_id' => ['nullable', 'integer', 'exists:writings,id'],

            'image' => ['nullable', 'image', 'max:20480', 'mimes:jpg,jpeg,png,webp,avif'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title_tr' => 'başlık',
            'slug_tr' => 'kısa ad',
            'caption_tr' => 'alt yazı',
            'alt_text_tr' => 'alt metin (erişilebilirlik)',
            'credit' => 'künye',
            'source' => 'kaynak',
            'license' => 'lisans',
            'rights_notes' => 'hak notları',
            'location' => 'konum',
            'captured_at' => 'çekim tarihi',
            'writing_id' => 'bağlı yazı',
            'image' => 'fotoğraf',
        ];
    }
}
