<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use JonesRussell\NorthCloud\Http\Controllers\Admin\ArticleController;

class MiningArticleController extends ArticleController
{
    protected function indexQuery(): Builder
    {
        return parent::indexQuery()->with([
            'commodities',
            'companies',
            'miningJurisdiction',
            'miningCategories',
        ]);
    }

    protected function afterStore(Model $article, Request $request): void
    {
        $this->syncMiningRelations($article, $request);
    }

    protected function afterUpdate(Model $article, Request $request): void
    {
        $this->syncMiningRelations($article, $request);
    }

    private function syncMiningRelations(Model $article, Request $request): void
    {
        if ($request->has('commodities')) {
            $article->commodities()->sync($request->input('commodities', []));
        }

        if ($request->has('companies')) {
            $article->companies()->sync($request->input('companies', []));
        }

        if ($request->has('mining_categories')) {
            $article->miningCategories()->sync($request->input('mining_categories', []));
        }

        if ($request->has('mining_jurisdiction_id')) {
            $article->update(['mining_jurisdiction_id' => $request->input('mining_jurisdiction_id')]);
        }
    }
}
