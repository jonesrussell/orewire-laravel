<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiningJurisdiction extends Model
{
    /** @use HasFactory<\Database\Factories\MiningJurisdictionFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'country_code',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MiningJurisdiction::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MiningJurisdiction::class, 'parent_id');
    }

    public function miningArticles(): HasMany
    {
        return $this->hasMany(MiningArticle::class);
    }

    public function scopeRoots(Builder $query): void
    {
        $query->whereNull('parent_id');
    }
}
