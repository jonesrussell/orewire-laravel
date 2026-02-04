<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\DrillResult;
use App\Models\MiningArticle;
use App\Models\NewsSource;
use App\Services\Resolvers\CommodityResolver;
use App\Services\Resolvers\CompanyResolver;
use App\Services\Resolvers\MiningCategoryResolver;
use App\Services\Resolvers\MiningJurisdictionResolver;
use App\Services\Resolvers\NewsSourceResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MiningIngestService
{
    public function __construct(
        private NewsSourceResolver $newsSourceResolver,
        private MiningJurisdictionResolver $miningJurisdictionResolver,
        private CommodityResolver $commodityResolver,
        private CompanyResolver $companyResolver,
        private MiningCategoryResolver $miningCategoryResolver,
    ) {}

    /**
     * @return array{status: string, mining_article: MiningArticle}
     */
    public function handle(array $data): array
    {
        $start = microtime(true);
        $externalId = $data['id'] ?? 'unknown';
        $sourceUrl = $data['source'] ?? $data['canonical_url'] ?? $data['og_url'] ?? null;

        try {
            $result = DB::transaction(function () use ($data) {
                $newsSource = $this->newsSourceResolver->resolve((string) ($data['source'] ?? $this->fallbackSourceUrl($data)));
                $existing = MiningArticle::where('news_source_id', $newsSource->id)
                    ->where('external_id', $data['id'])
                    ->first();

                $jurisdiction = $this->miningJurisdictionResolver->resolve(
                    $data['jurisdictions'] ?? []
                );

                $commodities = $this->commodityResolver->resolve(
                    $data['commodities'] ?? $data['topics'] ?? []
                );

                $companies = $this->companyResolver->resolve(
                    $data['companies'] ?? []
                );

                $categories = $this->miningCategoryResolver->resolve(
                    array_merge(
                        $data['categories'] ?? [],
                        $data['topics'] ?? []
                    )
                );

                if ($existing) {
                    return $this->updateArticle($existing, $data, $commodities, $companies, $categories, $jurisdiction);
                }

                return $this->createArticle($data, $newsSource, $commodities, $companies, $categories, $jurisdiction);
            });

            $this->invalidateCache();
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            Log::info('Mining ingest success', [
                'external_id' => $externalId,
                'source' => $sourceUrl,
                'outcome' => $result['status'],
                'duration_ms' => $durationMs,
            ]);

            return $result;
        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            Log::error('Mining ingest failed', [
                'external_id' => $externalId,
                'source' => $sourceUrl,
                'outcome' => 'failed',
                'duration_ms' => $durationMs,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function fallbackSourceUrl(array $data): string
    {
        $channel = $data['publisher']['channel'] ?? 'articles:mining';
        $parts = explode(':', $channel);
        $name = $parts[1] ?? 'mining';
        $slug = Str::slug($name);

        return "https://{$slug}.example.com";
    }

    private function createArticle(
        array $data,
        NewsSource $newsSource,
        \Illuminate\Support\Collection $commodities,
        \Illuminate\Support\Collection $companies,
        \Illuminate\Support\Collection $categories,
        ?\App\Models\MiningJurisdiction $jurisdiction
    ): array {
        $slug = $this->generateSlug($data['title']);
        $article = MiningArticle::create([
            'news_source_id' => $newsSource->id,
            'mining_jurisdiction_id' => $jurisdiction?->id,
            'title' => $data['title'] ?? $data['og_title'] ?? 'Untitled',
            'excerpt' => $data['intro'] ?? $data['og_description'] ?? $data['description'] ?? null,
            'content' => $this->sanitizeContent($data['body'] ?? $data['raw_text'] ?? null),
            'url' => $this->getArticleUrl($data),
            'external_id' => $data['id'],
            'slug' => $slug,
            'image_url' => $data['og_image'] ?? null,
            'author' => $data['author'] ?? null,
            'published_at' => $this->getPublishedAt($data),
            'crawled_at' => now(),
            'metadata' => $this->buildMetadata($data),
            'is_featured' => false,
        ]);

        $this->syncRelations($article, $commodities, $companies, $categories);
        $this->syncDrillResults($article, $data['drill_results'] ?? [], $commodities, $companies);

        return ['status' => 'created', 'mining_article' => $article];
    }

    private function updateArticle(
        MiningArticle $article,
        array $data,
        \Illuminate\Support\Collection $commodities,
        \Illuminate\Support\Collection $companies,
        \Illuminate\Support\Collection $categories,
        ?\App\Models\MiningJurisdiction $jurisdiction
    ): array {
        $article->update([
            'mining_jurisdiction_id' => $jurisdiction?->id,
            'title' => $data['title'] ?? $data['og_title'] ?? $article->title,
            'excerpt' => $data['intro'] ?? $data['og_description'] ?? $data['description'] ?? $article->excerpt,
            'content' => $this->sanitizeContent($data['body'] ?? $data['raw_text'] ?? $article->content),
            'url' => $this->getArticleUrl($data) ?: $article->url,
            'image_url' => $data['og_image'] ?? $article->image_url,
            'author' => $data['author'] ?? $article->author,
            'published_at' => $this->getPublishedAt($data) ?? $article->published_at,
            'metadata' => $this->buildMetadata($data),
            'updated_via_ingest_at' => now(),
        ]);

        $this->syncRelations($article, $commodities, $companies, $categories);
        $article->drillResults()->delete();
        $this->syncDrillResults($article, $data['drill_results'] ?? [], $commodities, $companies);

        return ['status' => 'updated', 'mining_article' => $article];
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'article';
        }

        $slug = $base;
        $counter = 2;

        while (MiningArticle::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function getArticleUrl(array $data): string
    {
        $url = $data['canonical_url'] ?? $data['og_url'] ?? $data['source'] ?? null;
        if (! empty($url)) {
            return $url;
        }

        $id = $data['id'];
        $channel = $data['publisher']['channel'] ?? 'articles:mining';
        $channelSlug = str_replace(':', '-', $channel);

        return "https://{$channelSlug}.example.com/{$id}";
    }

    private function getPublishedAt(array $data): ?Carbon
    {
        $date = $data['published_date'] ?? $data['publisher']['published_at'] ?? null;
        if (! $date) {
            return null;
        }

        try {
            $parsed = Carbon::parse($date);

            return $parsed->year > 1970 ? $parsed : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function sanitizeContent(?string $content): ?string
    {
        if (! $content) {
            return null;
        }

        return strip_tags($content, '<p><br><a><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6>');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildMetadata(array $data): array
    {
        $metadata = [];

        if (isset($data['publisher']) && is_array($data['publisher'])) {
            $metadata['publisher'] = [
                'route_id' => $data['publisher']['route_id'] ?? null,
                'published_at' => $data['publisher']['published_at'] ?? null,
                'channel' => $data['publisher']['channel'] ?? null,
            ];
        }

        foreach (['quality_score', 'source_reputation', 'confidence', 'content_type', 'word_count', 'keywords'] as $key) {
            if (array_key_exists($key, $data)) {
                $metadata[$key] = $data[$key];
            }
        }

        return $metadata;
    }

    private function syncRelations(
        MiningArticle $article,
        \Illuminate\Support\Collection $commodities,
        \Illuminate\Support\Collection $companies,
        \Illuminate\Support\Collection $categories
    ): void {
        $article->commodities()->sync($commodities->pluck('id'));
        $article->companies()->sync($companies->pluck('id'));
        $article->miningCategories()->sync($categories->pluck('id'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $drillResults
     */
    private function syncDrillResults(
        MiningArticle $article,
        array $drillResults,
        \Illuminate\Support\Collection $commodities,
        \Illuminate\Support\Collection $companies
    ): void {
        foreach ($drillResults as $row) {
            $commodity = null;
            $commodityName = trim((string) ($row['commodity'] ?? ''));
            if ($commodityName !== '') {
                $slug = Str::slug(Str::lower($commodityName));
                $commodity = Commodity::query()
                    ->whereRaw('LOWER(slug) = ?', [Str::lower($slug)])
                    ->first();
            }

            DrillResult::create([
                'mining_article_id' => $article->id,
                'commodity_id' => $commodity?->id,
                'company_id' => null,
                'hole_id' => $row['hole_id'] ?? null,
                'intercept_m' => $row['intercept_m'] ?? null,
                'grade' => $row['grade'] ?? null,
                'unit' => $row['unit'] ?? 'g/t',
            ]);
        }
    }

    private function invalidateCache(): void
    {
        try {
            if (config('cache.default') !== 'array') {
                Cache::tags(['drillfeed:homepage', 'drillfeed:commodities', 'drillfeed:companies'])->flush();
            } else {
                Cache::forget('drillfeed:homepage');
                Cache::forget('drillfeed:commodities');
                Cache::forget('drillfeed:companies');
            }
        } catch (\Throwable) {
            Cache::forget('drillfeed:homepage');
            Cache::forget('drillfeed:commodities');
            Cache::forget('drillfeed:companies');
        }
    }
}
