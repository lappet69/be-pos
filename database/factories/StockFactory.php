<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $product = new \App\Models\Product();
        $product = \App\Models\Product::factory()->create();
        return [
            'product_id' => $product->id,
            'stock' => 0,
        ];
    }
}
