<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Mining article from the north-cloud pipeline.
 *
 * @property array|null $metadata Allowed keys: quality_score, source_reputation, confidence,
 *                                content_type, publisher (route_id, channel, published_at), word_count, keywords.
 *                                No ad-hoc dumping. Promote frequently queried fields to columns.
 */
class MiningArticle extends Model
{
    /** @use HasFactory<\Database\Factories\MiningArticleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'news_source_id',
        'mining_jurisdiction_id',
        'title',
        'excerpt',
        'content',
        'url',
        'external_id',
        'slug',
        'image_url',
        'author',
        'published_at',
        'crawled_at',
        'metadata',
        'view_count',
        'is_featured',
        'updated_via_ingest_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'crawled_at' => 'datetime',
            'updated_via_ingest_at' => 'datetime',
            'metadata' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function newsSource(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class);
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

    public function scopeForDisplay(Builder $query): void
    {
        $query->with([
            'newsSource',
            'miningJurisdiction',
            'commodities',
            'companies',
            'miningCategories',
            'drillResults',
        ]);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where(function (Builder $q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })->orderByDesc('published_at');
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    public function scopeWithCommodity(Builder $query, string $slug): void
    {
        $query->whereHas('commodities', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithCompany(Builder $query, string $slug): void
    {
        $query->whereHas('companies', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithCategory(Builder $query, string $slug): void
    {
        $query->whereHas('miningCategories', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeWithJurisdiction(Builder $query, string $slug): void
    {
        $query->whereHas('miningJurisdiction', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    public function scopeSearch(Builder $query, string $searchTerm): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $term = '%'.$searchTerm.'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term)
                    ->orWhere('content', 'like', $term);
            });
        } else {
            $query->whereFullText(['title', 'excerpt', 'content'], $searchTerm);
        }
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
