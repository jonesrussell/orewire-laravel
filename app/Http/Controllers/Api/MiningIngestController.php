<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMiningArticleRequest;
use Illuminate\Http\JsonResponse;
use JonesRussell\NorthCloud\Processing\ProcessorPipeline;

class MiningIngestController extends Controller
{
    public function __construct(
        private ProcessorPipeline $pipeline
    ) {}

    public function store(StoreMiningArticleRequest $request): JsonResponse
    {
        $article = $this->pipeline->run($request->validated());

        if (! $article) {
            return response()->json(['error' => 'Article not processed'], 422);
        }

        return response()->json([
            'id' => $article->id,
            'slug' => $article->slug,
            'status' => $article->wasRecentlyCreated ? 'created' : 'updated',
        ]);
    }
}
