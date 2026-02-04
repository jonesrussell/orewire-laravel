<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MiningCategory extends Model
{
    /** @use HasFactory<\Database\Factories\MiningCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function miningArticles(): BelongsToMany
    {
        return $this->belongsToMany(MiningArticle::class, 'mining_article_mining_category')
            ->withTimestamps();
    }
}
