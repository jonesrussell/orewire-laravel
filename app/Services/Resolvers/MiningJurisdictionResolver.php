<?php

namespace App\Services\Resolvers;

use App\Models\MiningJurisdiction;
use Illuminate\Support\Str;

class MiningJurisdictionResolver
{
    /**
     * Resolve jurisdictions array (e.g. ["Canada", "Ontario"]) and return the most specific.
     * Processes in order: parent before child. Returns the last (most specific) jurisdiction.
     */
    public function resolve(array $jurisdictions): ?MiningJurisdiction
    {
        if (empty($jurisdictions)) {
            return null;
        }

        $jurisdictions = array_values(array_filter(array_map('trim', $jurisdictions)));
        $parent = null;

        foreach ($jurisdictions as $name) {
            $slug = Str::slug($name);
            $jurisdiction = MiningJurisdiction::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => Str::title($name),
                    'country_code' => null,
                    'parent_id' => $parent?->id,
                ]
            );

            if ($jurisdiction->wasRecentlyCreated && $parent) {
                $jurisdiction->update(['parent_id' => $parent->id]);
            }

            $parent = $jurisdiction;
        }

        return $parent;
    }
}
