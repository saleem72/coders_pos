<?php

namespace Database\Factories;

use App\Models\Category;
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
            //
            'category_id' => Category::factory(),
            'unit_id' => 1,
            'name' => $this->faker->company(),
            'purchase' => $this->faker->randomFloat(),
            'retail' => $this->faker->randomFloat(),
            'quantity' => $this->faker->randomNumber(),
            'barcode' => $this->faker->isbn13(),
            'image' => NULL,
            'image_extension' => NULL
        ];
    }
}
