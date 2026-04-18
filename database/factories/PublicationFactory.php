<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name'       => $name,
            'slug'       => Str::slug($name),
            'url'        => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
