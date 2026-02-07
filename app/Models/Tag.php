<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JonesRussell\NorthCloud\Models\Tag as BaseTag;

class Tag extends BaseTag
{
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(MiningArticle::class, 'article_tag', 'tag_id', 'article_id')
            ->withPivot('confidence')
            ->withTimestamps();
    }
}
