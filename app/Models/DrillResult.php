<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrillResult extends Model
{
    /** @use HasFactory<\Database\Factories\DrillResultFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('withoutSoftDeletedArticles', function (Builder $query) {
            $query->whereHas('miningArticle');
        });
    }

    protected $fillable = [
        'mining_article_id',
        'commodity_id',
        'company_id',
        'hole_id',
        'intercept_m',
        'grade',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'intercept_m' => 'decimal:2',
            'grade' => 'decimal:4',
        ];
    }

    public function miningArticle(): BelongsTo
    {
        return $this->belongsTo(MiningArticle::class);
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
