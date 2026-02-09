<?php

namespace App\Services;

use App\Models\CommodityPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommodityPriceService
{
    /** @var array<string, array{name: string, key: string}> */
    private const METALS = [
        'XAU' => ['name' => 'Gold', 'key' => 'gold'],
        'XAG' => ['name' => 'Silver', 'key' => 'silver'],
        'XCU' => ['name' => 'Copper', 'key' => 'copper'],
    ];

    public function fetchMetalPrices(): int
    {
        $apiKey = config('services.metals_dev.api_key');

        if (! $apiKey) {
            Log::warning('CommodityPriceService: METALS_DEV_API_KEY not configured');

            return 0;
        }

        $baseUrl = config('services.metals_dev.base_url');

        try {
            $response = Http::timeout(10)
                ->retry(2, 1000)
                ->get("{$baseUrl}/latest", [
                    'api_key' => $apiKey,
                    'currency' => 'USD',
                    'unit' => 'toz',
                ]);

            if (! $response->successful()) {
                Log::warning('CommodityPriceService: Metals.Dev API returned '.$response->status());

                return 0;
            }

            $metals = $response->json('metals', []);
            $updated = 0;

            foreach (self::METALS as $symbol => $meta) {
                $price = $metals[$meta['key']] ?? null;

                if ($price === null) {
                    continue;
                }

                $this->upsertPrice($symbol, $meta['name'], (float) $price);
                $updated++;
            }

            return $updated;
        } catch (\Throwable $e) {
            Log::warning('CommodityPriceService: Metals.Dev API error', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    private function upsertPrice(string $symbol, string $name, float $newPrice): void
    {
        $existing = CommodityPrice::query()->where('symbol', $symbol)->first();

        $change = null;
        if ($existing && (float) $existing->price_usd > 0) {
            $change = round((($newPrice - (float) $existing->price_usd) / (float) $existing->price_usd) * 100, 4);
        }

        CommodityPrice::query()->updateOrCreate(
            ['symbol' => $symbol],
            [
                'name' => $name,
                'type' => 'metal',
                'previous_price_usd' => $existing?->price_usd,
                'price_usd' => $newPrice,
                'change_24h_percent' => $change,
                'fetched_at' => now(),
            ],
        );
    }
}
