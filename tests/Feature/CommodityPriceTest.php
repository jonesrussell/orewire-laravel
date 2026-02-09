<?php

use App\Models\CommodityPrice;
use App\Services\CommodityPriceService;
use Illuminate\Support\Facades\Http;

it('fetches metal prices from metals.dev', function () {
    config(['services.metals_dev.api_key' => 'test-key']);

    Http::fake([
        'api.metals.dev/*' => Http::response([
            'status' => 'success',
            'currency' => 'USD',
            'unit' => 'toz',
            'metals' => [
                'gold' => 2650.50,
                'silver' => 30.25,
                'copper' => 4.12,
            ],
        ]),
    ]);

    $service = app(CommodityPriceService::class);
    $count = $service->fetchMetalPrices();

    expect($count)->toBe(3);
    expect(CommodityPrice::count())->toBe(3);

    $gold = CommodityPrice::where('symbol', 'XAU')->first();
    expect($gold->name)->toBe('Gold');
    expect($gold->type)->toBe('metal');
    expect((float) $gold->price_usd)->toBe(2650.50);
    expect($gold->fetched_at)->not->toBeNull();
});

it('computes change from previous price', function () {
    config(['services.metals_dev.api_key' => 'test-key']);

    CommodityPrice::factory()->gold()->create(['price_usd' => 2600.00]);

    Http::fake([
        'api.metals.dev/*' => Http::response([
            'status' => 'success',
            'metals' => [
                'gold' => 2650.50,
                'silver' => 30.25,
                'copper' => 4.12,
            ],
        ]),
    ]);

    $service = app(CommodityPriceService::class);
    $service->fetchMetalPrices();

    $gold = CommodityPrice::where('symbol', 'XAU')->first();
    expect((float) $gold->price_usd)->toBe(2650.50);
    expect((float) $gold->previous_price_usd)->toBe(2600.00);
    expect((float) $gold->change_24h_percent)->toBeGreaterThan(0);
});

it('handles api failure gracefully', function () {
    config(['services.metals_dev.api_key' => 'test-key']);

    CommodityPrice::factory()->gold()->create();

    Http::fake([
        'api.metals.dev/*' => Http::response([], 500),
    ]);

    $service = app(CommodityPriceService::class);
    $count = $service->fetchMetalPrices();

    expect($count)->toBe(0);
    expect(CommodityPrice::count())->toBe(1);
});

it('returns zero when api key missing', function () {
    config(['services.metals_dev.api_key' => null]);

    $service = app(CommodityPriceService::class);
    $count = $service->fetchMetalPrices();

    expect($count)->toBe(0);
});

it('runs prices:fetch command successfully', function () {
    config(['services.metals_dev.api_key' => 'test-key']);

    Http::fake([
        'api.metals.dev/*' => Http::response([
            'status' => 'success',
            'metals' => ['gold' => 2650.50, 'silver' => 30.25, 'copper' => 4.12],
        ]),
    ]);

    $this->artisan('prices:fetch')
        ->expectsOutputToContain('Updated 3 metal prices')
        ->assertSuccessful();

    expect(CommodityPrice::count())->toBe(3);
});

it('passes commodity prices to homepage', function () {
    CommodityPrice::factory()->gold()->create();
    CommodityPrice::factory()->silver()->create();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Home')
        ->has('commodityPrices', 2)
    );
});
