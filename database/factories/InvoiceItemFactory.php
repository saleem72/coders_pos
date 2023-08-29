<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => $this->faker->numberBetween(1,50),
            'price' => $this->faker->randomFloat(2,5000, 50000),
            'quantity' => $this->faker->numberBetween(1, 12),
            'notes' => $this->faker->sentence(),
        ];
    }
}
