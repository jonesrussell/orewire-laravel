<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyPageController extends Controller
{
    public function show(Request $request, Company $company): Response
    {
        $articles = $company->miningArticles()
            ->with(['newsSource', 'miningJurisdiction', 'commodities', 'miningCategories'])
            ->published()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Companies/Show', [
            'company' => $company,
            'articles' => $articles,
        ]);
    }
}
