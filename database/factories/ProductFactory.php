<?php

namespace Database\Factories;

use Database\Seeders\BrandSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
        $categoryQuantity = (new CategorySeeder)->quantity;
        $brandQuantity = (new BrandSeeder)->quantity;
        $userQuantity = (new UserSeeder)->quantity;
        return [
            'code' => fake()->ean8(),
            'title' => fake()->text(15),
            'description' => fake()->text(),
            'price' => fake()->numberBetween(1, 50000),
            'price_unit' => '$',
            'weight' => fake()->numberBetween(1, 3000),
            'weight_unit' => 'g',
            'width' => fake()->numberBetween(1, 150),
            'height' => fake()->numberBetween(1, 150),
            'large' => fake()->numberBetween(1, 150),
            'dimension_unit' => 'cm',
            'material' => fake()->text(8),
            'color' => fake()->safeColorName(),
            'package_units' => fake()->numberBetween(1, 30),
            'stock' => fake()->numberBetween(1, 100),
            'brand_id' => fake()->numberBetween(1, $brandQuantity),
            'category_id' => fake()->numberBetween(1, $categoryQuantity),
            'user_id' => fake()->numberBetween(2, $userQuantity),
        ];
    }
}
