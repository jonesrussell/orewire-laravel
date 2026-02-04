<?php

use App\Models\MiningArticle;
use App\Models\NewsSource;
use Database\Seeders\CommoditySeeder;
use Database\Seeders\MiningCategorySeeder;

beforeEach(function () {
    $this->seed(CommoditySeeder::class);
    $this->seed(MiningCategorySeeder::class);
});

it('creates article from valid payload', function () {
    config(['drillfeed.ingest_token' => 'test-token']);

    $payload = [
        'id' => 'es-doc-123',
        'title' => 'Company X Reports Gold Drill Results',
        'body' => 'Full content here',
        'intro' => 'Excerpt here',
        'canonical_url' => 'https://miningnews.com/article',
        'source' => 'https://miningnews.com',
        'published_date' => '2025-02-04T10:00:00Z',
        'publisher' => [
            'route_id' => 'route-1',
            'published_at' => '2025-02-04T12:00:00Z',
            'channel' => 'articles:mining',
        ],
        'commodities' => ['gold'],
        'companies' => ['Company X Inc'],
    ];

    $response = $this->postJson('/api/ingest/mining-article', $payload, [
        'Authorization' => 'Bearer test-token',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'status' => 'created',
    ]);
    $response->assertJsonStructure(['id', 'slug', 'status']);

    $article = MiningArticle::where('external_id', 'es-doc-123')->first();
    expect($article)->not->toBeNull();
    expect($article->title)->toBe('Company X Reports Gold Drill Results');
    expect($article->content)->toBe('Full content here');
    expect($article->commodities)->toHaveCount(1);
    expect($article->commodities->first()->slug)->toBe('gold');
});

it('returns 401 without valid token', function () {
    config(['drillfeed.ingest_token' => 'secret']);

    $response = $this->postJson('/api/ingest/mining-article', [
        'id' => 'es-doc-1',
        'title' => 'Test',
    ], [
        'Authorization' => 'Bearer wrong-token',
    ]);

    $response->assertUnauthorized();
});

it('returns 401 without authorization header', function () {
    config(['drillfeed.ingest_token' => 'secret']);

    $response = $this->postJson('/api/ingest/mining-article', [
        'id' => 'es-doc-1',
        'title' => 'Test',
    ]);

    $response->assertUnauthorized();
});

it('validates required id and title', function () {
    config(['drillfeed.ingest_token' => 'test-token']);

    $response = $this->postJson('/api/ingest/mining-article', [], [
        'Authorization' => 'Bearer test-token',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['id', 'title']);
});

it('updates existing article on re-ingestion', function () {
    config(['drillfeed.ingest_token' => 'test-token']);

    $source = NewsSource::create([
        'name' => 'Mining News',
        'slug' => 'miningnews',
        'url' => 'https://miningnews.com',
        'is_active' => true,
    ]);

    $article = MiningArticle::create([
        'news_source_id' => $source->id,
        'external_id' => 'es-doc-update',
        'title' => 'Original Title',
        'slug' => 'original-title',
        'url' => 'https://example.com/original',
        'published_at' => now(),
    ]);

    $payload = [
        'id' => 'es-doc-update',
        'title' => 'Updated Title',
        'body' => 'Updated content',
        'source' => $source->url,
        'published_date' => '2025-02-04T10:00:00Z',
    ];

    $response = $this->postJson('/api/ingest/mining-article', $payload, [
        'Authorization' => 'Bearer test-token',
    ]);

    $response->assertSuccessful();
    $response->assertJson(['status' => 'updated']);

    $article->refresh();
    expect($article->title)->toBe('Updated Title');
    expect($article->content)->toBe('Updated content');
});
