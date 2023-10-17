<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feeds>
 */
class FeedsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typeOption = [
            'UltraPack',
            'Starter',
            'Grower',
            'Fattener',
            'Breeder',
        ];

        $nameOption = [
            'UltraPack',
            'Emmanuel',
            'Pigrolac',
            'ProBio',
            'B-MEG',
            'Master Mix',
        ];

        return [
            'name' => fake()->randomElement($nameOption),
            'type' => fake()->randomElement($typeOption),
            'stocks' => fake()->numberBetween('0', '50'),
            'bought_price' => fake()->numberBetween('900', '1000'),
            'selling_price' => fake()->numberBetween('1000', '1500'),
        ];
    }
}
