<?php

namespace App\Processing;

use App\Models\Commodity;
use App\Models\DrillResult;
use App\Models\MiningArticle;
use App\Services\Resolvers\CommodityResolver;
use App\Services\Resolvers\CompanyResolver;
use App\Services\Resolvers\MiningCategoryResolver;
use App\Services\Resolvers\MiningJurisdictionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JonesRussell\NorthCloud\Contracts\ArticleModel;
use JonesRussell\NorthCloud\Contracts\ArticleProcessor;
use JonesRussell\NorthCloud\Services\ArticleIngestionService;

class MiningArticleProcessor implements ArticleProcessor
{
    public function __construct(
        protected ArticleIngestionService $ingestionService,
        protected MiningJurisdictionResolver $jurisdictionResolver,
        protected CommodityResolver $commodityResolver,
        protected CompanyResolver $companyResolver,
        protected MiningCategoryResolver $categoryResolver,
    ) {}

    public function shouldProcess(array $data): bool
    {
        return true;
    }

    public function process(array $data, ?ArticleModel $article): ?Model
    {
        if ($this->isDuplicate($data)) {
            return null;
        }

        $article = $this->ingestionService->ingest($data, skipDedup: true);

        if (! $article) {
            return null;
        }

        $this->resolveRelations($article, $data);
        $this->mergeMetadata($article, $data);
        $this->syncDrillResults($article, $data);
        $this->invalidateCache();

        return $article;
    }

    protected function isDuplicate(array $data): bool
    {
        $title = $data['title'] ?? $data['og_title'] ?? null;
        if (! $title) {
            return false;
        }

        $normalized = self::normalizeTitle($title);

        $query = MiningArticle::whereRaw('LOWER(title) LIKE ?', [$normalized.'%']);

        $publishedDate = $data['published_date'] ?? $data['publisher']['published_at'] ?? null;
        if ($publishedDate) {
            try {
                $date = Carbon::parse($publishedDate);
                $query->whereBetween('published_at', [
                    $date->copy()->subDay(),
                    $date->copy()->addDay(),
                ]);
            } catch (\Exception) {
                // No date constraint if unparseable
            }
        }

        if ($query->exists()) {
            Log::info('Skipping duplicate syndicated article', [
                'title' => $title,
                'normalized' => $normalized,
            ]);

            return true;
        }

        return false;
    }

    public static function normalizeTitle(string $title): string
    {
        // Strip trailing " - Source Name" suffix (after last " - ")
        $lastDash = strrpos($title, ' - ');
        if ($lastDash !== false) {
            $title = substr($title, 0, $lastDash);
        }

        return mb_strtolower(trim($title));
    }

    protected function resolveRelations(MiningArticle $article, array $data): void
    {
        $jurisdiction = $this->jurisdictionResolver->resolve($data['jurisdictions'] ?? []);
        if ($jurisdiction) {
            $article->update(['mining_jurisdiction_id' => $jurisdiction->id]);
        }

        $commodities = $this->commodityResolver->resolve(
            $data['commodities'] ?? $data['topics'] ?? []
        );
        $article->commodities()->sync($commodities->pluck('id'));

        $companies = $this->companyResolver->resolve($data['companies'] ?? []);
        $article->companies()->sync($companies->pluck('id'));

        $categories = $this->categoryResolver->resolve(
            array_merge($data['categories'] ?? [], $data['topics'] ?? [])
        );
        $article->miningCategories()->sync($categories->pluck('id'));
    }

    protected function mergeMetadata(MiningArticle $article, array $data): void
    {
        $existing = $article->metadata ?? [];
        $extra = [];

        foreach (['confidence', 'content_type', 'word_count', 'keywords'] as $field) {
            if (isset($data[$field])) {
                $extra[$field] = $data[$field];
            }
        }

        if (! empty($extra)) {
            $article->update(['metadata' => array_merge($existing, $extra)]);
        }
    }

    protected function syncDrillResults(MiningArticle $article, array $data): void
    {
        $drillResults = $data['drill_results'] ?? [];
        if (empty($drillResults)) {
            return;
        }

        $article->drillResults()->delete();

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

    protected function invalidateCache(): void
    {
        try {
            if (config('cache.default') !== 'array') {
                Cache::tags(['orewire:homepage', 'orewire:commodities', 'orewire:companies'])->flush();
            } else {
                Cache::forget('orewire:homepage');
                Cache::forget('orewire:commodities');
                Cache::forget('orewire:companies');
            }
        } catch (\Throwable) {
            Cache::forget('orewire:homepage');
            Cache::forget('orewire:commodities');
            Cache::forget('orewire:companies');
        }
    }
}
