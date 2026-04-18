<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    /** @var array<int, string> */
    public array $translatable = [
        'title',
        'intro',
        'body',
        'meta_title',
        'meta_description',
    ];

    public const KINDS = ['system', 'custom'];

    public const TEMPLATES = ['default', 'about', 'contact'];

    protected $fillable = [
        'slug',
        'kind',
        'template',
        'title',
        'intro',
        'body',
        'meta_title',
        'meta_description',
        'extras',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'extras'       => 'array',
            'is_published' => 'boolean',
            'sort_order'   => 'integer',
        ];
    }

    public function url(): string
    {
        return '/'.$this->slug;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /** Typed accessor for the extras JSON bag. */
    public function extra(string $key, mixed $default = null): mixed
    {
        $extras = $this->extras ?? [];

        return $extras[$key] ?? $default;
    }
}
