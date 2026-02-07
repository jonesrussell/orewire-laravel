<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIngestToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('orewire.ingest_token');

        if (empty($token)) {
            return response()->json(['error' => 'Ingest not configured'], 503);
        }

        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $providedToken = substr($authHeader, 7);
        if (! hash_equals($token, $providedToken)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
