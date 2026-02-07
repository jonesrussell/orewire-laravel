<?php

use App\Http\Controllers\CommodityPageController;
use App\Http\Controllers\CompanyPageController;
use App\Http\Controllers\DrillResultPageController;
use App\Http\Controllers\MiningArticlePageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [MiningArticlePageController::class, 'home'])->name('home');

Route::get('/articles', [MiningArticlePageController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [MiningArticlePageController::class, 'show'])->name('articles.show');

Route::get('/commodities/{commodity:slug}', [CommodityPageController::class, 'show'])->name('commodities.show');
Route::get('/companies/{company:slug}', [CompanyPageController::class, 'show'])->name('companies.show');
Route::get('/drill-results', [DrillResultPageController::class, 'index'])->name('drill-results.index');

Route::get('dashboard', function () {
    return redirect()->route('dashboard.articles.index');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
