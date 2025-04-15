<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'stripe_id' => $this->faker->slug,
            'features' => $this->faker->sentence,
            'interval' => $this->faker->randomElement(['monthly', 'yearly']),
            'currency' => $this->faker->currencyCode,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'price_description' => $this->faker->sentence,
            'description' => $this->faker->sentence,
        ];
    }
}
