<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commodity extends Model
{
    /** @use HasFactory<\Database\Factories\CommodityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'symbol',
    ];

    public function miningArticles(): BelongsToMany
    {
        return $this->belongsToMany(MiningArticle::class, 'mining_article_commodity')
            ->withTimestamps();
    }

    public function drillResults(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DrillResult::class);
    }
}
