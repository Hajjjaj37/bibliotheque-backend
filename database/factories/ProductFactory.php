<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['X', 'Pro', 'Max', 'Lite']),
            'description' => $this->faker->sentence(10), // Description aléatoire
            'price' => $this->faker->randomFloat(2, 10), // Prix aléatoire entre 50 et 2000
            'stock' => $this->faker->numberBetween(0, 100), // Quantité en stock aléatoire
            'category_id' => $this->faker->numberBetween(1, 8), // ID de catégorie aléatoire
            'image' => $this->faker->imageUrl(640, 480, 'technics', true, 'products'), // Image aléatoire
            'average_rating' => $this->faker->randomFloat(1, 1, 5), // Note moyenne aléatoire entre 1 et 5
        ];
    }
}
