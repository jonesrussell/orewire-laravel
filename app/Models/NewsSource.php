<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use JonesRussell\NorthCloud\Models\NewsSource as BaseNewsSource;

class NewsSource extends BaseNewsSource
{
    public function miningArticles(): HasMany
    {
        return $this->hasMany(MiningArticle::class);
    }
}
