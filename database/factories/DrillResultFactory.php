<?php

namespace Database\Factories;

use App\Models\DrillResult;
use App\Models\MiningArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DrillResult> */
class DrillResultFactory extends Factory
{
    protected $model = DrillResult::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'mining_article_id' => MiningArticle::factory(),
            'commodity_id' => null,
            'company_id' => null,
            'hole_id' => 'DDH-'.fake()->numerify('##-###'),
            'intercept_m' => fake()->randomFloat(2, 1, 50),
            'grade' => fake()->randomFloat(4, 0.1, 20),
            'unit' => fake()->randomElement(['g/t', '%', 'ppm']),
        ];
    }
}
