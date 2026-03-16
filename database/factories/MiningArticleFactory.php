<?php

namespace Database\Factories;

use App\Models\MiningArticle;
use App\Models\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MiningArticle> */
class MiningArticleFactory extends Factory
{
    protected $model = MiningArticle::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'content' => fake()->paragraphs(3, true),
            'excerpt' => fake()->sentence(),
            'url' => fake()->url(),
            'image_url' => fake()->imageUrl(),
            'news_source_id' => NewsSource::factory(),
            'published_at' => fake()->dateTimeBetween('-1 month'),
            'external_id' => fake()->uuid(),
        ];
    }
}
