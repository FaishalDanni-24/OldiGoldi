<?php

namespace Database\Factories\Products;

use App\Models\Products\Product;
use App\Models\Products\Category;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'sku' => strtoupper(Str::random(10)),
            'product_name' => fake()->words(3, true),
            'brand' => fake()->optional()->company(),
            'model' => fake()->optional()->bothify('Model-###'),
            'product_description' => fake()->optional()->paragraph(),
            'condition_grade' => fake()->randomElement(['like_new', 'good', 'fair', 'for_parts']),
            'condition_notes' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 50000, 10000000),
            'stock' => fake()->numberBetween(0, 10),
            'product_status' => fake()->randomElement(['draft', 'active', 'sold_out', 'inactive']),
        ];
    }
}
