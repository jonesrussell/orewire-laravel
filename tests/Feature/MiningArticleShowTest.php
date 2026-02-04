<?php

use App\Models\MiningArticle;
use App\Models\NewsSource;

it('loads article show page', function () {
    $source = NewsSource::create([
        'name' => 'Test Source',
        'slug' => 'test-source',
        'url' => 'https://test.com',
        'is_active' => true,
    ]);

    $article = MiningArticle::create([
        'news_source_id' => $source->id,
        'external_id' => 'ext-1',
        'title' => 'Test Mining Article',
        'slug' => 'test-mining-article',
        'url' => 'https://test.com/article',
        'published_at' => now(),
    ]);

    $response = $this->get("/articles/{$article->slug}");

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Articles/Show')
        ->has('article')
        ->where('article.title', 'Test Mining Article')
    );
});
