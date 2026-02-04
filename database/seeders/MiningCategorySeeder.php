<?php

namespace Database\Seeders;

use App\Models\MiningCategory;
use Illuminate\Database\Seeder;

class MiningCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Exploration', 'slug' => 'exploration', 'type' => 'category'],
            ['name' => 'Financing', 'slug' => 'financing', 'type' => 'category'],
            ['name' => 'Development', 'slug' => 'development', 'type' => 'category'],
            ['name' => 'Production', 'slug' => 'production', 'type' => 'category'],
            ['name' => 'Mergers & Acquisitions', 'slug' => 'mergers', 'type' => 'category'],
            ['name' => 'Drill Results', 'slug' => 'drill-results', 'type' => 'category'],
        ];

        foreach ($categories as $category) {
            MiningCategory::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                ]
            );
        }
    }
}
