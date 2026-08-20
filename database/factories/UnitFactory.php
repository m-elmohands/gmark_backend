<?php

namespace Database\Factories;

use App\Enums\Availability;
use App\Enums\ContentStatus;
use App\Models\Area;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id' => Area::factory(),
            'title' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'reference_code' => fake()->unique()->bothify('GM-###??'),
            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(3, true),
            'unit_type' => fake()->randomElement(['Apartment', 'Duplex', 'Villa']),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 4),
            'indoor_area' => fake()->randomFloat(2, 80, 450),
            'area_unit' => 'sqm',
            'availability' => Availability::Available,
            'features' => ['Private terrace', 'Open-plan living'],
            'status' => ContentStatus::Draft,
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
