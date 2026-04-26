<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        $titleTr = fake()->sentence(4);
        $titleEn = fake()->sentence(4);
        $slug = Str::slug($titleTr);

        return [
            'title' => ['tr' => $titleTr, 'en' => $titleEn],
            'caption' => ['tr' => fake()->sentence(14), 'en' => fake()->sentence(14)],
            'alt_text' => ['tr' => fake()->sentence(8),  'en' => fake()->sentence(8)],
            'slug' => ['tr' => $slug, 'en' => $slug],
            'credit' => 'Foto: Ozan Efeoğlu / AA',
            'source' => 'AA',
            'license' => 'editorial-only',
            'rights_notes' => null,
            'location' => fake()->city(),
            'captured_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'kind' => fake()->randomElement(Photo::KINDS),
            'is_published' => true,
            'is_featured' => false,
            'hero_eligible' => false,
            'is_demo' => false,
            'writing_id' => null,
            'sort_order' => 0,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => ['is_published' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['is_published' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }

    public function heroEligible(): static
    {
        return $this->state(fn (): array => ['hero_eligible' => true]);
    }

    public function demo(): static
    {
        return $this->state(fn (): array => ['is_demo' => true]);
    }

    public function ofKind(string $kind): static
    {
        return $this->state(fn (): array => ['kind' => $kind]);
    }
}
