<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MiningArticle;
use App\Processing\MiningArticleProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class DeduplicateArticles extends Command
{
    protected $signature = 'mining:deduplicate
        {--dry-run : Preview duplicates without deleting}
        {--cleanup-non-mining : Also remove known non-mining articles (e.g. French finance)}';

    protected $description = 'Soft-delete duplicate syndicated articles, keeping the earliest-ingested copy per title';

    /** Titles (substring match, case-insensitive) of known non-mining articles to remove. */
    private const NON_MINING_TITLES = [
        'investir en 2026',
    ];

    public function handle(): int
    {
        if ($this->option('cleanup-non-mining')) {
            $this->cleanupNonMining();
        }

        $groups = $this->findDuplicateGroups();

        if ($groups->isEmpty()) {
            $this->info('No duplicate articles found.');

            return Command::SUCCESS;
        }

        $totalDuplicates = $groups->sum(fn (Collection $group) => $group->count() - 1);

        $this->info("Found {$groups->count()} duplicate groups ({$totalDuplicates} articles to remove).");
        $this->newLine();

        $this->showGroups($groups);

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no articles deleted. Run without --dry-run to delete.');

            return Command::SUCCESS;
        }

        $deleted = $this->deleteDuplicates($groups);
        $this->info("Soft-deleted {$deleted} duplicate articles.");

        return Command::SUCCESS;
    }

    private function findDuplicateGroups(): Collection
    {
        $articles = MiningArticle::query()
            ->select(['id', 'title', 'published_at', 'crawled_at', 'url'])
            ->orderBy('crawled_at')
            ->get();

        // Group by normalized title + published date (same day)
        $grouped = $articles->groupBy(function (MiningArticle $article) {
            $normalized = MiningArticleProcessor::normalizeTitle($article->title);
            $date = $article->published_at?->toDateString() ?? 'no-date';

            return $normalized.'|'.$date;
        });

        // Only keep groups with more than one article
        return $grouped->filter(fn (Collection $group) => $group->count() > 1);
    }

    private function showGroups(Collection $groups): void
    {
        foreach ($groups as $key => $group) {
            $keep = $group->first();
            $dupes = $group->slice(1);

            $this->line("  <info>KEEP</info>: {$keep->title}");
            $this->line("         url: {$keep->url}");
            $this->line("         crawled: {$keep->crawled_at}");

            foreach ($dupes as $dupe) {
                $this->line("  <comment>DELETE</comment>: {$dupe->title}");
                $this->line("           url: {$dupe->url}");
            }

            $this->newLine();
        }
    }

    private function deleteDuplicates(Collection $groups): int
    {
        $idsToDelete = [];

        foreach ($groups as $group) {
            // Skip the first (earliest crawled_at) — keep it
            $duplicates = $group->slice(1);
            foreach ($duplicates as $article) {
                $idsToDelete[] = $article->id;
            }
        }

        return MiningArticle::whereIn('id', $idsToDelete)->delete();
    }

    private function cleanupNonMining(): void
    {
        $query = MiningArticle::query()
            ->where(function ($q) {
                foreach (self::NON_MINING_TITLES as $title) {
                    $q->orWhereRaw('LOWER(title) LIKE ?', ['%'.$title.'%']);
                }
            });

        $count = $query->count();

        if ($count === 0) {
            $this->info('No known non-mining articles found.');

            return;
        }

        $query->clone()->each(function (MiningArticle $article) {
            $this->line("  <comment>NON-MINING</comment>: {$article->title}");
        });

        if ($this->option('dry-run')) {
            $this->warn("Would remove {$count} non-mining article(s).");
            $this->newLine();

            return;
        }

        $deleted = $query->delete();
        $this->info("Removed {$deleted} non-mining article(s).");
        $this->newLine();
    }
}
