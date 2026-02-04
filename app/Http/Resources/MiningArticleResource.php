<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MiningArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'author' => $this->author,
            'published_at' => $this->published_at?->toIso8601String(),
            'news_source' => $this->whenLoaded('newsSource', fn () => [
                'id' => $this->newsSource->id,
                'name' => $this->newsSource->name,
                'slug' => $this->newsSource->slug,
            ]),
            'commodities' => CommodityResource::collection($this->whenLoaded('commodities')),
            'companies' => CompanyResource::collection($this->whenLoaded('companies')),
            'mining_categories' => MiningCategoryResource::collection($this->whenLoaded('miningCategories')),
            'mining_jurisdiction' => $this->whenLoaded('miningJurisdiction', fn () => $this->miningJurisdiction ? [
                'id' => $this->miningJurisdiction->id,
                'name' => $this->miningJurisdiction->name,
                'slug' => $this->miningJurisdiction->slug,
            ] : null),
            'drill_results' => DrillResultResource::collection($this->whenLoaded('drillResults')),
        ];
    }
}
