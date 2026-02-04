<?php

namespace App\Services\Resolvers;

use App\Models\MiningCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MiningCategoryResolver
{
    /**
     * Resolve categories/topics array.
     *
     * @return Collection<int, MiningCategory>
     */
    public function resolve(array $categories): Collection
    {
        if (empty($categories)) {
            return collect();
        }

        $results = collect();
        $seenIds = [];

        foreach ($categories as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $slug = Str::slug($name);
            $category = MiningCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => Str::title(str_replace('-', ' ', $slug)),
                    'type' => 'category',
                ]
            );

            if (! in_array($category->id, $seenIds, true)) {
                $results->push($category);
                $seenIds[] = $category->id;
            }
        }

        return $results;
    }
}
