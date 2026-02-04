<?php

namespace App\Services\Resolvers;

use App\Models\Company;
use App\Support\CompanyNameNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CompanyResolver
{
    /**
     * Resolve companies array. Match by symbol first if available, else normalized slug.
     *
     * @return Collection<int, Company>
     */
    public function resolve(array $companies): Collection
    {
        if (empty($companies)) {
            return collect();
        }

        $results = collect();
        $seenIds = [];

        foreach ($companies as $item) {
            $name = is_array($item) ? ($item['name'] ?? $item['symbol'] ?? '') : (string) $item;
            $symbol = is_array($item) ? ($item['symbol'] ?? null) : null;

            $name = trim($name);
            if ($name === '') {
                continue;
            }

            $company = $this->findOrCreateCompany($name, $symbol);
            if ($company && ! in_array($company->id, $seenIds, true)) {
                $results->push($company);
                $seenIds[] = $company->id;
            }
        }

        return $results;
    }

    private function findOrCreateCompany(string $name, ?string $symbol): ?Company
    {
        if ($symbol !== null && $symbol !== '') {
            $company = Company::where('symbol', $symbol)->first();
            if ($company) {
                return $company;
            }
        }

        $slug = CompanyNameNormalizer::slug($name);
        if ($slug === '') {
            return null;
        }

        return Company::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => Str::title($name),
                'symbol' => $symbol,
            ]
        );
    }
}
