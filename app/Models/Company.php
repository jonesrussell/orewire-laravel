<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'symbol',
    ];

    public function miningArticles(): BelongsToMany
    {
        return $this->belongsToMany(MiningArticle::class, 'mining_article_company')
            ->withTimestamps();
    }

    public function drillResults(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DrillResult::class);
    }
}
