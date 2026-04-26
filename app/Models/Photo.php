<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * Photo — archive unit for the /görüntü surface.
 *
 * A published photo is a standalone editorial object: it has its own
 * slug, caption, credit, location, captured_at. It may optionally be
 * linked to a Writing (the text dispatch that accompanies it) but does
 * not require one.
 *
 * Media: one "image" collection per Photo row; conversions at 640 /
 * 1280 / 1920 / 2560 for editorial grid + detail view.
 *
 * Legal: credit + source + license tracked in columns, not in JSON, so
 * admin lists and public pages can reason about rights at query time.
 * Default source for demo seeds is "AA" (Anadolu Ajansı).
 */
class Photo extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use InteractsWithMedia;
    use SoftDeletes;

    public const KINDS = ['reportage', 'portrait', 'drone', 'protocol', 'editorial', 'archive'];

    public const LICENSES = ['editorial-only', 'cc-by', 'all-rights-reserved'];

    /** @var array<int, string> */
    public array $translatable = [
        'title',
        'caption',
        'alt_text',
        'slug',
    ];

    protected $fillable = [
        'title',
        'caption',
        'alt_text',
        'slug',
        'credit',
        'source',
        'license',
        'rights_notes',
        'location',
        'captured_at',
        'kind',
        'is_published',
        'is_featured',
        'hero_eligible',
        'is_demo',
        'writing_id',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'hero_eligible' => 'boolean',
            'is_demo' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Slug
    // ---------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        // Slug is translatable (JSON). If admin/seeder already set a slug,
        // honor it — only fall back to title-derived generation when the
        // slug field is empty. Without this guard HasSlug overwrites the
        // admin's chosen slug on create() and the photo becomes reachable
        // only via its auto-derived URL.
        return SlugOptions::create()
            ->generateSlugsFrom(function (Photo $p): string {
                $existing = trim((string) $p->getTranslation('slug', 'tr', false));
                if ($existing !== '') {
                    return $existing;
                }

                return (string) ($p->getTranslation('title', 'tr', false) ?? '');
            })
            ->saveSlugsTo('slug->tr')
            ->slugsShouldBeNoLongerThan(80)
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function url(): string
    {
        $slug = $this->getTranslation('slug', app()->getLocale(), false)
            ?? $this->getTranslation('slug', 'tr', false)
            ?? (string) $this->id;

        return route('visuals.show', ['slug' => $slug]);
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function writing(): BelongsTo
    {
        return $this->belongsTo(Writing::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeHeroEligible(Builder $q): Builder
    {
        return $q->where('hero_eligible', true);
    }

    public function scopeKind(Builder $q, ?string $kind): Builder
    {
        if ($kind === null || $kind === '' || ! in_array($kind, self::KINDS, true)) {
            return $q;
        }

        return $q->where('kind', $kind);
    }

    // ---------------------------------------------------------------
    // Resolver helpers
    // ---------------------------------------------------------------

    public function resolvedCredit(): string
    {
        $own = trim((string) ($this->credit ?? ''));
        if ($own !== '') {
            return $own;
        }

        return (string) site_setting('photo.default_credit', 'Foto: Ozan Efeoğlu');
    }

    public function resolvedAltText(): string
    {
        $alt = trim((string) $this->getTranslationWithFallback('alt_text'));
        if ($alt !== '') {
            return $alt;
        }

        $title = trim((string) $this->getTranslationWithFallback('title'));

        return $title !== '' ? $title : __('Fotoğraf');
    }

    /**
     * Translation with Turkish fallback when current locale has no value.
     */
    public function getTranslationWithFallback(string $field): ?string
    {
        $locale = app()->getLocale();
        $value = $this->getTranslation($field, $locale, false);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $this->getTranslation($field, 'tr', false);
    }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            'reportage' => 'röportaj',
            'portrait' => 'portre',
            'drone' => 'drone',
            'protocol' => 'protokol',
            'editorial' => 'editoryal',
            'archive' => 'arşiv',
            default => (string) $this->kind,
        };
    }

    // ---------------------------------------------------------------
    // Media library — single image per photo
    // ---------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach ([640, 1280, 1920, 2560] as $width) {
            $this->addMediaConversion("w{$width}")
                ->width($width)
                ->format('webp')
                ->quality($width > 1600 ? 70 : 78)
                ->performOnCollections('image')
                ->nonQueued();
        }

        // A tiny placeholder for LQIP / lazy loading.
        $this->addMediaConversion('thumb')
            ->width(32)
            ->format('webp')
            ->quality(40)
            ->performOnCollections('image')
            ->nonQueued();
    }

    public function imageUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('image');
        if (! $media) {
            return null;
        }

        return $conversion !== '' && $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    public function imageSrcset(): ?string
    {
        $media = $this->getFirstMedia('image');
        if (! $media) {
            return null;
        }

        $parts = [];
        foreach ([640, 1280, 1920, 2560] as $w) {
            $conv = "w{$w}";
            if ($media->hasGeneratedConversion($conv)) {
                $parts[] = $media->getUrl($conv)." {$w}w";
            }
        }

        return $parts ? implode(', ', $parts) : null;
    }

    public function hasImage(): bool
    {
        return $this->getFirstMedia('image') !== null;
    }
}
