<?php

use App\Models\Commodity;
use App\Models\Company;
use Database\Seeders\CommoditySeeder;
use Database\Seeders\MiningCategorySeeder;

beforeEach(function () {
    $this->seed(CommoditySeeder::class);
    $this->seed(MiningCategorySeeder::class);
});

it('loads articles index', function () {
    $response = $this->get('/articles');

    $response->assertSuccessful();
});

it('loads drill results index', function () {
    $response = $this->get('/drill-results');

    $response->assertSuccessful();
});

it('loads commodity show page', function () {
    $commodity = Commodity::first();

    $response = $this->get("/commodities/{$commodity->slug}");

    $response->assertSuccessful();
});

it('loads company show page', function () {
    $company = Company::create([
        'name' => 'Test Mining Co',
        'slug' => 'test-mining-co',
    ]);

    $response = $this->get("/companies/{$company->slug}");

    $response->assertSuccessful();
});
