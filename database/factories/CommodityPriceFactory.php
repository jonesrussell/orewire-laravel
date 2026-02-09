<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommodityPrice>
 */
class CommodityPriceFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'symbol' => fake()->unique()->lexify('???'),
            'name' => fake()->word(),
            'type' => fake()->randomElement(['metal', 'crypto']),
            'price_usd' => fake()->randomFloat(2, 1, 100000),
            'previous_price_usd' => fake()->randomFloat(2, 1, 100000),
            'change_24h_percent' => fake()->randomFloat(4, -10, 10),
            'fetched_at' => now(),
        ];
    }

    public function gold(): static
    {
        return $this->state(fn () => [
            'symbol' => 'XAU',
            'name' => 'Gold',
            'type' => 'metal',
            'price_usd' => fake()->randomFloat(2, 1800, 2800),
        ]);
    }

    public function silver(): static
    {
        return $this->state(fn () => [
            'symbol' => 'XAG',
            'name' => 'Silver',
            'type' => 'metal',
            'price_usd' => fake()->randomFloat(2, 20, 35),
        ]);
    }

    public function copper(): static
    {
        return $this->state(fn () => [
            'symbol' => 'XCU',
            'name' => 'Copper',
            'type' => 'metal',
            'price_usd' => fake()->randomFloat(2, 3, 5),
        ]);
    }

    public function bitcoin(): static
    {
        return $this->state(fn () => [
            'symbol' => 'BTC',
            'name' => 'Bitcoin',
            'type' => 'crypto',
            'price_usd' => fake()->randomFloat(2, 30000, 120000),
        ]);
    }

    public function ethereum(): static
    {
        return $this->state(fn () => [
            'symbol' => 'ETH',
            'name' => 'Ethereum',
            'type' => 'crypto',
            'price_usd' => fake()->randomFloat(2, 1500, 5000),
        ]);
    }
}
