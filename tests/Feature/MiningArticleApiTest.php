<?php

use Database\Seeders\CommoditySeeder;
use Database\Seeders\MiningCategorySeeder;

beforeEach(function () {
    $this->seed(CommoditySeeder::class);
    $this->seed(MiningCategorySeeder::class);
});

it('returns articles list from api', function () {
    $response = $this->getJson('/api/articles');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data',
        'links',
        'meta',
    ]);
});

it('returns commodities list from api', function () {
    $response = $this->getJson('/api/commodities');

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
});

it('returns companies list from api', function () {
    $response = $this->getJson('/api/companies');

    $response->assertSuccessful();
});

it('returns categories list from api', function () {
    $response = $this->getJson('/api/categories');

    $response->assertSuccessful();
});
