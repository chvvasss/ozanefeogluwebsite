<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\Writing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Writing>
 */
class WritingFactory extends Factory
{
    protected $model = Writing::class;

    public function definition(): array
    {
        $title = fake()->sentence(5);
        $slug = Str::slug($title);
        $body = '<p>'.implode("</p>\n<p>", fake()->paragraphs(5)).'</p>';

        return [
            'author_id'        => User::factory(),
            'kind'             => fake()->randomElement(Writing::KINDS),
            'status'           => 'published',
            'published_at'     => fake()->dateTimeBetween('-2 years', 'now'),
            'location'         => fake()->city(),
            'title'            => ['tr' => $title, 'en' => $title],
            'slug'             => ['tr' => $slug, 'en' => $slug],
            'excerpt'          => ['tr' => fake()->sentence(12), 'en' => fake()->sentence(12)],
            'body'             => ['tr' => $body, 'en' => $body],
            'meta_title'       => null,
            'meta_description' => null,
            'canonical_url'    => null,
            'cover_hue_a'      => fake()->numberBetween(0, 255),
            'cover_hue_b'      => fake()->numberBetween(0, 255),
            'is_featured'      => false,
            'sort_order'       => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft', 'published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status'       => 'scheduled',
            'published_at' => now()->addDays(3),
        ]);
    }

    public function ofKind(string $kind): static
    {
        return $this->state(fn (): array => ['kind' => $kind]);
    }
}
