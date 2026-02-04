<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMiningArticleRequest;
use App\Services\MiningIngestService;
use Illuminate\Http\JsonResponse;

class MiningIngestController extends Controller
{
    public function __construct(
        private MiningIngestService $ingestService
    ) {}

    public function store(StoreMiningArticleRequest $request): JsonResponse
    {
        $result = $this->ingestService->handle($request->validated());

        return response()->json([
            'id' => $result['mining_article']->id,
            'slug' => $result['mining_article']->slug,
            'status' => $result['status'],
        ]);
    }
}
