<?php

namespace App\Models;

use Database\Factories\MiningArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use JonesRussell\NorthCloud\Models\Article;

/**
 * Mining article from the north-cloud pipeline.
 *
 * @property array|null $metadata Allowed keys: quality_score, source_reputation, confidence,
 *                                content_type, publisher (route_id, channel, published_at), word_count, keywords.
 *                                No ad-hoc dumping. Promote frequently queried fields to columns.
 */
class MiningArticle extends Article
{
    protected $table = 'mining_articles';

    protected static function newFactory(): Factory
    {
        return MiningArticleFactory::new();
    }

    protected $fillable = [
        'news_source_id',
        'mining_jurisdiction_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'url',
        'external_id',
        'image_url',
        'author',
        'status',
        'published_at',
        'crawled_at',
        'metadata',
        'view_count',
        'is_featured',
        'updated_via_ingest_at',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'updated_via_ingest_at' => 'datetime',
        ]);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag', 'article_id', 'tag_id')
            ->withPivot('confidence')
            ->withTimestamps();
    }

    public function miningJurisdiction(): BelongsTo
    {
        return $this->belongsTo(MiningJurisdiction::class);
    }

    public function commodities(): BelongsToMany
    {
        return $this->belongsToMany(Commodity::class, 'mining_article_commodity')
            ->withTimestamps();
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'mining_article_company')
            ->withTimestamps();
    }

    public function miningCategories(): BelongsToMany
    {
        return $this->belongsToMany(MiningCategory::class, 'mining_article_mining_category')
            ->withTimestamps();
    }

    public function drillResults(): HasMany
    {
        return $this->hasMany(DrillResult::class);
    }

    public function scopeForDisplay(Builder $query): Builder
    {
        return $query->with([
            'newsSource',
            'miningJurisdiction',
            'commodities',
            'companies',
            'miningCategories',
            'drillResults',
        ]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })->orderByDesc('published_at');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithCommodity(Builder $query, string $slug): Builder
    {
        return $query->whereHas('commodities', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithCompany(Builder $query, string $slug): Builder
    {
        return $query->whereHas('companies', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('miningCategories', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithJurisdiction(Builder $query, string $slug): Builder
    {
        return $query->whereHas('miningJurisdiction', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        if (DB::getDriverName() === 'sqlite') {
            $escaped = '%'.$term.'%';

            return $query->where(function (Builder $q) use ($escaped) {
                $q->where('title', 'like', $escaped)
                    ->orWhere('excerpt', 'like', $escaped)
                    ->orWhere('content', 'like', $escaped);
            });
        }

        return $query->whereFullText(['title', 'excerpt', 'content'], $term);
    }
}
