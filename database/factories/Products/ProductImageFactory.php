<?php

namespace Database\Factories\Products;

use App\Models\Products\Product;
use App\Models\Products\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image_url' => fake()->imageUrl(640, 480, 'technics'),
            'is_primary' => fake()->boolean(20),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
