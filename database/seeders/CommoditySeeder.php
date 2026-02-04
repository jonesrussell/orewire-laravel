<?php

namespace Database\Seeders;

use App\Models\Commodity;
use Illuminate\Database\Seeder;

class CommoditySeeder extends Seeder
{
    public function run(): void
    {
        $commodities = [
            ['name' => 'Gold', 'slug' => 'gold', 'symbol' => 'AU'],
            ['name' => 'Silver', 'slug' => 'silver', 'symbol' => 'AG'],
            ['name' => 'Copper', 'slug' => 'copper', 'symbol' => 'CU'],
            ['name' => 'Zinc', 'slug' => 'zinc', 'symbol' => 'ZN'],
            ['name' => 'Nickel', 'slug' => 'nickel', 'symbol' => 'NI'],
            ['name' => 'Lithium', 'slug' => 'lithium', 'symbol' => 'LI'],
            ['name' => 'Uranium', 'slug' => 'uranium', 'symbol' => 'U'],
        ];

        foreach ($commodities as $commodity) {
            Commodity::firstOrCreate(
                ['slug' => $commodity['slug']],
                [
                    'name' => $commodity['name'],
                    'symbol' => $commodity['symbol'],
                ]
            );
        }
    }
}
