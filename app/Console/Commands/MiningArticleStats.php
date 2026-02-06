<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MiningArticle;
use Illuminate\Console\Command;

class MiningArticleStats extends Command
{
    protected $signature = 'mining:stats';

    protected $description = 'Print mining article ingestion statistics';

    public function handle(): int
    {
        $total = MiningArticle::count();
        $last24h = MiningArticle::where('crawled_at', '>=', now()->subDay())->count();
        $last7d = MiningArticle::where('crawled_at', '>=', now()->subDays(7))->count();
        $withDrillResults = MiningArticle::whereHas('drillResults')->count();
        $featured = MiningArticle::where('is_featured', true)->count();

        $latest = MiningArticle::orderByDesc('crawled_at')->first();

        $bySource = MiningArticle::query()
            ->join('news_sources', 'mining_articles.news_source_id', '=', 'news_sources.id')
            ->selectRaw('news_sources.name, COUNT(*) as count')
            ->groupBy('news_sources.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name');

        $this->info('Mining Article Statistics');
        $this->info('=======================');
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total articles', number_format($total)],
                ['Last 24 hours', number_format($last24h)],
                ['Last 7 days', number_format($last7d)],
                ['With drill results', number_format($withDrillResults)],
                ['Featured', number_format($featured)],
            ]
        );

        $this->newLine();
        $this->info('Top sources');
        $this->table(
            ['Source', 'Articles'],
            $bySource->map(fn (int $count, string $name) => [$name, number_format($count)])->values()->toArray()
        );

        if ($latest) {
            $this->newLine();
            $this->info('Latest article');
            $this->line("  Title: {$latest->title}");
            $this->line('  Crawled: '.$latest->crawled_at?->toIso8601String());
            $this->line("  Source: {$latest->newsSource?->name}");
        }

        return Command::SUCCESS;
    }
}
