<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MiningArticleResource;
use App\Models\MiningArticle;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MiningArticleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $articles = MiningArticle::query()
            ->forDisplay()
            ->published()
            ->paginate(20);

        return MiningArticleResource::collection($articles);
    }

    public function show(MiningArticle $miningArticle): MiningArticleResource
    {
        $miningArticle->load([
            'newsSource',
            'miningJurisdiction',
            'commodities',
            'companies',
            'miningCategories',
            'drillResults.commodity',
        ]);

        return new MiningArticleResource($miningArticle);
    }
}
