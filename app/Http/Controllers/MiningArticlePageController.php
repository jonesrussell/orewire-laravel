<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\CommodityPrice;
use App\Models\Company;
use App\Models\DrillResult;
use App\Models\MiningArticle;
use App\Models\MiningJurisdiction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class MiningArticlePageController extends Controller
{
    public function home(): Response
    {
        $cacheKey = 'orewire:homepage';
        $cacheTtl = 300;

        $data = Cache::remember($cacheKey, $cacheTtl, function () {
            $latestArticles = MiningArticle::query()
                ->forDisplay()
                ->published()
                ->limit(8)
                ->get();

            $latestDrillResults = DrillResult::query()
                ->with(['miningArticle', 'commodity'])
                ->latest()
                ->limit(5)
                ->get();

            $financings = MiningArticle::query()
                ->forDisplay()
                ->published()
                ->whereHas('miningCategories', fn ($q) => $q->where('slug', 'financing'))
                ->limit(5)
                ->get();

            $trendingCompanies = Company::query()
                ->withCount('miningArticles')
                ->orderByDesc('mining_articles_count')
                ->limit(5)
                ->get();

            $commodityPrices = CommodityPrice::query()
                ->orderByRaw("CASE symbol WHEN 'XAU' THEN 0 WHEN 'XAG' THEN 1 WHEN 'XCU' THEN 2 ELSE 3 END")
                ->get();

            return [
                'latestArticles' => $latestArticles,
                'latestDrillResults' => $latestDrillResults,
                'financings' => $financings,
                'trendingCompanies' => $trendingCompanies,
                'commodityPrices' => $commodityPrices,
            ];
        });

        return Inertia::render('Home', $data);
    }

    public function index(Request $request): Response
    {
        $articles = MiningArticle::query()
            ->forDisplay()
            ->published()
            ->when($request->commodity, fn ($q) => $q->withCommodity($request->commodity))
            ->when($request->company, fn ($q) => $q->withCompany($request->company))
            ->when($request->jurisdiction, fn ($q) => $q->withJurisdiction($request->jurisdiction))
            ->when($request->category, fn ($q) => $q->withCategory($request->category))
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->paginate(20)
            ->withQueryString();

        $commodities = Commodity::query()->orderBy('name')->get(['id', 'name', 'slug']);
        $jurisdictions = MiningJurisdiction::query()->orderBy('name')->get(['id', 'name', 'slug']);

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'commodities' => $commodities,
            'jurisdictions' => $jurisdictions,
            'filters' => [
                'commodity' => $request->commodity,
                'company' => $request->company,
                'jurisdiction' => $request->jurisdiction,
                'category' => $request->category,
                'search' => $request->search,
            ],
        ]);
    }

    public function show(MiningArticle $article): Response
    {
        $article->load([
            'newsSource',
            'miningJurisdiction',
            'commodities',
            'companies',
            'miningCategories',
            'drillResults.commodity',
        ]);
        $article->incrementViewCount();

        $relatedArticles = MiningArticle::query()
            ->forDisplay()
            ->published()
            ->where('id', '!=', $article->id)
            ->where(function ($query) use ($article) {
                $query->whereHas('commodities', fn ($q) => $q->whereIn('commodities.id', $article->commodities->pluck('id')))
                    ->orWhereHas('companies', fn ($q) => $q->whereIn('companies.id', $article->companies->pluck('id')))
                    ->orWhere('mining_jurisdiction_id', $article->mining_jurisdiction_id);
            })
            ->limit(6)
            ->get();

        return Inertia::render('Articles/Show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
