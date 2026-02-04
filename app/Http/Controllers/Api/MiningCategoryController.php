<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MiningCategoryResource;
use App\Models\MiningCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MiningCategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = MiningCategory::query()
            ->orderBy('name')
            ->get();

        return MiningCategoryResource::collection($categories);
    }
}
