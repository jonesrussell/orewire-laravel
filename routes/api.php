<?php

use App\Http\Controllers\Api\CommodityController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\MiningArticleController as ApiMiningArticleController;
use App\Http\Controllers\Api\MiningCategoryController;
use App\Http\Controllers\Api\MiningIngestController;
use Illuminate\Support\Facades\Route;

Route::post('/ingest/mining-article', [MiningIngestController::class, 'store'])
    ->middleware(['auth.ingest', 'throttle:orewire']);

Route::get('/articles', [ApiMiningArticleController::class, 'index']);
Route::get('/articles/{miningArticle}', [ApiMiningArticleController::class, 'show']);
Route::get('/commodities', [CommodityController::class, 'index']);
Route::get('/companies', [CompanyController::class, 'index']);
Route::get('/categories', [MiningCategoryController::class, 'index']);
