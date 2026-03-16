<?php

use App\Models\Commodity;
use App\Models\DrillResult;
use App\Processing\MiningArticleProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeProcessor(): MiningArticleProcessor
{
    return app(MiningArticleProcessor::class);
}

function makePayload(array $overrides = []): array
{
    return array_merge([
        'id' => fake()->uuid(),
        'title' => 'Test Mining Corp Reports Drill Results',
        'body' => 'Test article body about drill results.',
        'source' => 'https://example.com/article',
        'published_date' => now()->toIso8601String(),
        'og_title' => 'Test Mining Corp Reports Drill Results',
        'og_image' => 'https://example.com/image.jpg',
        'quality_score' => 85,
        'topics' => ['mining'],
        'commodities' => ['gold'],
        'companies' => [],
        'categories' => ['Mining'],
        'jurisdictions' => [],
    ], $overrides);
}

it('stores drill results from payload', function () {
    Commodity::factory()->create(['name' => 'Gold', 'slug' => 'gold']);

    $payload = makePayload([
        'drill_results' => [
            ['hole_id' => 'DDH-24-001', 'commodity' => 'gold', 'intercept_m' => 12.5, 'grade' => 3.2, 'unit' => 'g/t'],
            ['hole_id' => 'DDH-24-002', 'commodity' => 'gold', 'intercept_m' => 8.0, 'grade' => 1.8, 'unit' => 'g/t'],
            ['hole_id' => 'DDH-24-003', 'commodity' => 'gold', 'intercept_m' => 5.5, 'grade' => 6.1, 'unit' => 'g/t'],
        ],
    ]);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    expect($article)->not->toBeNull();
    expect(DrillResult::where('mining_article_id', $article->id)->count())->toBe(3);

    $first = DrillResult::where('hole_id', 'DDH-24-001')->first();
    expect($first->intercept_m)->toBe('12.50');
    expect($first->grade)->toBe('3.2000');
    expect($first->unit)->toBe('g/t');
    expect($first->commodity_id)->not->toBeNull();
});

it('handles empty drill_results array', function () {
    $payload = makePayload(['drill_results' => []]);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    expect($article)->not->toBeNull();
    expect(DrillResult::where('mining_article_id', $article->id)->count())->toBe(0);
});

it('handles missing drill_results key', function () {
    $payload = makePayload();
    // Explicitly remove drill_results to test backward compat
    unset($payload['drill_results']);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    expect($article)->not->toBeNull();
    expect(DrillResult::where('mining_article_id', $article->id)->count())->toBe(0);
});

it('resolves commodity by slug', function () {
    $gold = Commodity::factory()->create(['name' => 'Gold', 'slug' => 'gold']);

    $payload = makePayload([
        'drill_results' => [
            ['hole_id' => 'DDH-24-001', 'commodity' => 'gold', 'intercept_m' => 10.0, 'grade' => 2.0, 'unit' => 'g/t'],
        ],
    ]);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    $result = DrillResult::where('mining_article_id', $article->id)->first();
    expect($result->commodity_id)->toBe($gold->id);
});

it('handles unknown commodity gracefully', function () {
    $payload = makePayload([
        'drill_results' => [
            ['hole_id' => 'DDH-24-001', 'commodity' => 'unobtainium', 'intercept_m' => 10.0, 'grade' => 2.0, 'unit' => 'g/t'],
        ],
    ]);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    $result = DrillResult::where('mining_article_id', $article->id)->first();
    expect($result)->not->toBeNull();
    expect($result->commodity_id)->toBeNull();
});

it('replaces drill results on re-ingestion', function () {
    $processor = makeProcessor();

    // First ingestion: create article with 1 drill result
    $payload = makePayload([
        'title' => 'Re-ingestion Test Article',
        'drill_results' => [
            ['hole_id' => 'DDH-24-001', 'commodity' => 'gold', 'intercept_m' => 10.0, 'grade' => 2.0, 'unit' => 'g/t'],
        ],
    ]);
    $article = $processor->process($payload, null);
    expect($article)->not->toBeNull();
    expect(DrillResult::where('mining_article_id', $article->id)->count())->toBe(1);

    // Directly call syncDrillResults on the same article with different results
    $newData = [
        'drill_results' => [
            ['hole_id' => 'DDH-24-010', 'commodity' => 'gold', 'intercept_m' => 5.0, 'grade' => 1.0, 'unit' => 'g/t'],
            ['hole_id' => 'DDH-24-011', 'commodity' => 'gold', 'intercept_m' => 8.0, 'grade' => 3.0, 'unit' => 'g/t'],
        ],
    ];

    // Use reflection to call protected syncDrillResults on the same article
    $method = new \ReflectionMethod($processor, 'syncDrillResults');
    $method->invoke($processor, $article, $newData);

    // Old results should be replaced, not appended
    expect(DrillResult::where('mining_article_id', $article->id)->count())->toBe(2);
    expect(DrillResult::where('hole_id', 'DDH-24-001')->count())->toBe(0);
    expect(DrillResult::where('hole_id', 'DDH-24-010')->count())->toBe(1);
});

it('stores various unit types correctly', function () {
    $payload = makePayload([
        'drill_results' => [
            ['hole_id' => 'DDH-24-001', 'commodity' => 'gold', 'intercept_m' => 10.0, 'grade' => 3.2, 'unit' => 'g/t'],
            ['hole_id' => 'DDH-24-002', 'commodity' => 'copper', 'intercept_m' => 8.0, 'grade' => 1.5, 'unit' => '%'],
            ['hole_id' => 'DDH-24-003', 'commodity' => 'gold', 'intercept_m' => 5.0, 'grade' => 850.0, 'unit' => 'ppm'],
            ['hole_id' => 'DDH-24-004', 'commodity' => 'gold', 'intercept_m' => 12.0, 'grade' => 0.15, 'unit' => 'oz/t'],
        ],
    ]);

    $processor = makeProcessor();
    $article = $processor->process($payload, null);

    $results = DrillResult::where('mining_article_id', $article->id)->get();
    expect($results)->toHaveCount(4);

    $units = $results->pluck('unit')->sort()->values()->all();
    expect($units)->toBe(['%', 'g/t', 'oz/t', 'ppm']);
});
