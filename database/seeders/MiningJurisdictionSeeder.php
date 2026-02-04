<?php

namespace Database\Seeders;

use App\Models\MiningJurisdiction;
use Illuminate\Database\Seeder;

class MiningJurisdictionSeeder extends Seeder
{
    public function run(): void
    {
        $jurisdictions = [
            ['name' => 'Canada', 'slug' => 'canada', 'country_code' => 'CA', 'parent_id' => null],
            ['name' => 'Ontario', 'slug' => 'ontario', 'country_code' => 'CA', 'parent_slug' => 'canada'],
            ['name' => 'Quebec', 'slug' => 'quebec', 'country_code' => 'CA', 'parent_slug' => 'canada'],
            ['name' => 'British Columbia', 'slug' => 'british-columbia', 'country_code' => 'CA', 'parent_slug' => 'canada'],
            ['name' => 'USA', 'slug' => 'usa', 'country_code' => 'US', 'parent_id' => null],
            ['name' => 'Nevada', 'slug' => 'nevada', 'country_code' => 'US', 'parent_slug' => 'usa'],
            ['name' => 'Arizona', 'slug' => 'arizona', 'country_code' => 'US', 'parent_slug' => 'usa'],
            ['name' => 'Australia', 'slug' => 'australia', 'country_code' => 'AU', 'parent_id' => null],
            ['name' => 'Western Australia', 'slug' => 'western-australia', 'country_code' => 'AU', 'parent_slug' => 'australia'],
        ];

        foreach ($jurisdictions as $data) {
            $parentId = null;
            if (isset($data['parent_slug'])) {
                $parent = MiningJurisdiction::where('slug', $data['parent_slug'])->first();
                $parentId = $parent?->id;
            }

            MiningJurisdiction::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'country_code' => $data['country_code'],
                    'parent_id' => $parentId ?? ($data['parent_id'] ?? null),
                ]
            );
        }
    }
}
