<?php

namespace Database\Factories;

use Database\Seeders\ProductSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productQuantity = (new ProductSeeder)->quantity;
        return [
            'quantity' => fake()->numberBetween(1, 30),
            'product_id' => fake()->numberBetween(1, $productQuantity),
            'user_id' => 3,
        ];
    }
}
