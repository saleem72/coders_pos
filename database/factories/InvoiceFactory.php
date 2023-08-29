<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timeZone = $this->faker->timezone('US');
        return [
            'customer_id' => $this->faker->numberBetween(1,5),
            'invoice_date' => $this->faker->dateTimeThisYear($timeZone),
            'tax' => $this->faker->randomFloat(0,1),
            'subtotal' => $this->faker->randomFloat(10000,100000),
            'notes' => $this->faker->sentence(),
        ];
    }
}
