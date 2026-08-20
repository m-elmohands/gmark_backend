<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().' Residences',
            'slug' => fake()->unique()->slug(3),
            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city(),
            'highlights' => ['Landscaped gardens', 'Natural light'],
            'amenities' => ['Residents lounge', 'Walking paths'],
            'status' => ContentStatus::Draft,
            'is_featured' => false,
            'display_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::Published,
            'published_at' => now()->subMinute(),
        ]);
    }
}
