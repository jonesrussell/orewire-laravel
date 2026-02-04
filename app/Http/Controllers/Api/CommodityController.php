<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommodityResource;
use App\Models\Commodity;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommodityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $commodities = Commodity::query()
            ->orderBy('name')
            ->get();

        return CommodityResource::collection($commodities);
    }
}
