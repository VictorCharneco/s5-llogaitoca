<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instrument>
 */
class InstrumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['STRING', 'WIND', 'PERCUSSION', 'KEYBOARD']),
            'status' => $this->faker->randomElement(['AVAILABLE', 'OUT_OF_STOCK', 'MAINTENANCE']),
            'image_path' => null,
        ];
    }
}
