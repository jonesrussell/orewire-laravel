<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommodityPageController extends Controller
{
    public function show(Request $request, Commodity $commodity): Response
    {
        $articles = $commodity->miningArticles()
            ->with(['newsSource', 'miningJurisdiction', 'companies', 'miningCategories'])
            ->published()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Commodities/Show', [
            'commodity' => $commodity,
            'articles' => $articles,
        ]);
    }
}
