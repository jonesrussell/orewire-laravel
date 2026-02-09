<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MiningArticle;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CleanupNonMiningArticles extends Command
{
    protected $signature = 'mining:cleanup
        {--dry-run : Preview affected articles without deleting}';

    protected $description = 'Soft-delete articles with no mining relations (commodities, companies, categories, drill results, jurisdiction)';

    public function handle(): int
    {
        $query = $this->buildNonMiningQuery();

        $total = $query->count();

        if ($total === 0) {
            $this->info('No non-mining articles found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$total} articles with no mining relations.");
        $this->newLine();

        $this->showSample($query);

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no articles deleted. Run without --dry-run to delete.');

            return Command::SUCCESS;
        }

        if (! $this->confirm("Soft-delete {$total} non-mining articles?")) {
            $this->info('Cancelled.');

            return Command::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Soft-deleted {$deleted} articles.");

        return Command::SUCCESS;
    }

    /**
     * REGEXP patterns for mining keywords with word boundaries.
     * Uses MariaDB [[:<:]] / [[:>:]] word boundary syntax to avoid
     * substring matches (e.g. "miner" matching "Examiner").
     */
    private const MINING_PATTERNS = [
        '[[:<:]]mining[[:>:]]',
        '[[:<:]]miner[[:>:]]',
        '[[:<:]]mine[[:>:]]',
        '[[:<:]]mineral[[:>:]]',
        '[[:<:]]exploration[[:>:]]',
        '[[:<:]]drilling[[:>:]]',
        '[[:<:]]ore[[:>:]]',
        '[[:<:]]orebody[[:>:]]',
        '[[:<:]]assay[[:>:]]',
        '[[:<:]]open-pit[[:>:]]',
        '[[:<:]]tailings[[:>:]]',
        '[[:<:]]smelter[[:>:]]',
        '[[:<:]]refinery[[:>:]]',
        '[[:<:]]metallurgy[[:>:]]',
        '[[:<:]]metallurgical[[:>:]]',
        '[[:<:]]concentrate[[:>:]]',
    ];

    private function buildNonMiningQuery(): Builder
    {
        return MiningArticle::query()
            ->whereDoesntHave('commodities')
            ->whereDoesntHave('companies')
            ->whereDoesntHave('drillResults')
            ->whereNull('mining_jurisdiction_id')
            ->where(function (Builder $q) {
                foreach (self::MINING_PATTERNS as $pattern) {
                    $q->where('title', 'NOT REGEXP', $pattern);
                }
            });
    }

    private function showSample(Builder $query): void
    {
        $sampleSize = 10;
        $samples = $query->clone()
            ->select(['id', 'title', 'url', 'published_at'])
            ->orderByDesc('published_at')
            ->limit($sampleSize)
            ->get();

        $this->table(
            ['ID', 'Title', 'URL', 'Published'],
            $samples->map(fn (MiningArticle $a) => [
                $a->id,
                str($a->title)->limit(60),
                str($a->url)->limit(50),
                $a->published_at?->toDateString() ?? 'N/A',
            ])->toArray()
        );
    }
}
