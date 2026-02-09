<?php

namespace App\Console\Commands;

use App\Services\CommodityPriceService;
use Illuminate\Console\Command;

class FetchCommodityPrices extends Command
{
    protected $signature = 'prices:fetch';

    protected $description = 'Fetch latest metal prices from Metals.Dev API';

    public function handle(CommodityPriceService $service): int
    {
        $count = $service->fetchMetalPrices();
        $this->info("Updated {$count} metal prices.");

        return self::SUCCESS;
    }
}
