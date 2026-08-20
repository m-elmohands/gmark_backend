<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Location> */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'slug' => Str::slug($name),
            'latitude' => fake()->latitude(22, 31.5),
            'longitude' => fake()->longitude(25, 35),
            'is_active' => true,
            'en' => ['name' => $name, 'description' => fake()->sentence()],
            'ar' => ['name' => 'موقع '.$name, 'description' => 'وجهة سكنية مميزة في مصر.'],
        ];
    }
}
