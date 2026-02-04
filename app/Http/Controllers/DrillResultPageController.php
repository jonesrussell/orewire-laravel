<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\DrillResult;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DrillResultPageController extends Controller
{
    public function index(Request $request): Response
    {
        $drillResults = DrillResult::query()
            ->with(['miningArticle', 'commodity', 'company'])
            ->when($request->commodity, fn ($q) => $q->whereHas('commodity', fn ($c) => $c->where('slug', $request->commodity)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $commodities = Commodity::query()->orderBy('name')->get(['id', 'name', 'slug']);

        return Inertia::render('DrillResults/Index', [
            'drillResults' => $drillResults,
            'commodities' => $commodities,
            'filters' => [
                'commodity' => $request->commodity,
            ],
        ]);
    }
}
