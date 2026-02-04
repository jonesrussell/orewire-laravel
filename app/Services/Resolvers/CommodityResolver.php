<?php

namespace App\Services\Resolvers;

use App\Models\Commodity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CommodityResolver
{
    /**
     * Resolve commodities array. Case-insensitive slug match to avoid duplicates.
     *
     * @return Collection<int, Commodity>
     */
    public function resolve(array $commodities): Collection
    {
        if (empty($commodities)) {
            return collect();
        }

        $results = collect();
        $seenIds = [];

        foreach ($commodities as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $slug = Str::slug(Str::lower($name));
            $commodity = Commodity::query()
                ->whereRaw('LOWER(slug) = ?', [Str::lower($slug)])
                ->first();

            if ($commodity && ! in_array($commodity->id, $seenIds, true)) {
                $results->push($commodity);
                $seenIds[] = $commodity->id;
            }
        }

        return $results;
    }
}
