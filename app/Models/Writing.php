<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Writing extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;
    use SoftDeletes;

    /**
     * Writing kind taxonomy — press photographer + editor output categories.
     * `saha_yazisi` retained for legacy data backward-compat but no longer
     * seeded or surfaced in admin dropdowns (Phase A positioning correction).
     */
    public const KINDS = ['foto_notu', 'editoryal', 'analiz', 'roportaj', 'deneme', 'not'];

    public const STATUSES = ['draft', 'scheduled', 'published'];

    /** @var array<int, string> */
    public array $translatable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'meta_title',
        'meta_description',
        'cover_caption',
    ];

    protected $fillable = [
        'author_id',
        'kind',
        'status',
        'published_at',
        'location',
        'title',
        'slug',
        'excerpt',
        'body',
        'meta_title',
        'meta_description',
        'canonical_url',
        'read_minutes',
        'is_featured',
        'is_demo',
        'hero_eligible',
        'photo_credit',
        'cover_caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_demo' => 'boolean',
            'hero_eligible' => 'boolean',
            'read_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class)
            ->withPivot('link')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfKind(Builder $query, ?string $kind): Builder
    {
        return $kind && in_array($kind, self::KINDS, true)
            ? $query->where('kind', $kind)
            : $query;
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        $locale = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'tr');

        return $query->where(function (Builder $q) use ($slug, $locale, $fallback): void {
            $q->where("slug->{$locale}", $slug);
            if ($locale !== $fallback) {
                $q->orWhere("slug->{$fallback}", $slug);
            }
        });
    }

    // ---------------------------------------------------------------
    // Accessors / computed
    // ---------------------------------------------------------------

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            'foto_notu' => 'foto notu',
            'editoryal' => 'editoryal',
            'analiz' => 'analiz',
            'roportaj' => 'röportaj',
            'deneme' => 'deneme',
            'not' => 'not',
            'saha_yazisi' => 'saha yazısı', // legacy
            default => (string) $this->kind,
        };
    }

    /**
     * Resolved photo credit. Per-item override wins; otherwise global default
     * from config('site.default_photo_credit'). Never null unless the
     * writing has no cover at all (caller's responsibility to check).
     */
    public function resolvedPhotoCredit(): string
    {
        $override = trim((string) ($this->photo_credit ?? ''));

        return $override !== ''
            ? $override
            : (string) config('site.default_photo_credit', 'Foto: Ozan Efeoğlu / AA');
    }

    public function url(): string
    {
        $slug = (string) $this->getTranslation('slug', app()->getLocale(), false);
        if ($slug === '') {
            $slug = (string) $this->getTranslation('slug', (string) config('app.fallback_locale', 'tr'), false);
        }

        return $slug !== '' ? "/yazilar/{$slug}" : '#';
    }

    public function publishedDate(): Carbon
    {
        return $this->published_at ?? $this->created_at ?? now();
    }

    // ---------------------------------------------------------------
    // Lifecycle — auto read-time calculation
    // ---------------------------------------------------------------

    protected static function booted(): void
    {
        static::saving(function (Writing $writing): void {
            $fallback = (string) config('app.fallback_locale', 'tr');
            $bodies = $writing->getTranslations('body');

            $plain = '';
            foreach ([$fallback, ...array_keys($bodies)] as $locale) {
                $candidate = (string) ($bodies[$locale] ?? '');
                if ($candidate !== '') {
                    $plain = trim(strip_tags($candidate));
                    break;
                }
            }

            $writing->read_minutes = self::calculateReadMinutes($plain);
        });
    }

    private static function calculateReadMinutes(string $plain): int
    {
        if ($plain === '') {
            return 1;
        }

        $words = preg_split('/\s+/u', $plain) ?: [];

        return max(1, (int) ceil(count($words) / 220));
    }

    // ---------------------------------------------------------------
    // Media library — cover image
    // ---------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach ([640, 1280, 1920] as $width) {
            $this->addMediaConversion("w{$width}")
                ->width($width)
                ->format('webp')
                ->quality($width > 1500 ? 72 : 78)
                ->performOnCollections('cover')
                ->nonQueued();
        }
    }

    public function coverUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('cover');
        if (! $media) {
            return null;
        }

        return $conversion !== '' && $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    public function coverSrcset(): ?string
    {
        $media = $this->getFirstMedia('cover');
        if (! $media) {
            return null;
        }

        $parts = [];
        foreach ([640 => 'w640', 1280 => 'w1280', 1920 => 'w1920'] as $w => $conv) {
            if ($media->hasGeneratedConversion($conv)) {
                $parts[] = $media->getUrl($conv)." {$w}w";
            }
        }

        return $parts ? implode(', ', $parts) : null;
    }

    public function hasCover(): bool
    {
        return $this->getFirstMedia('cover') !== null;
    }
}
