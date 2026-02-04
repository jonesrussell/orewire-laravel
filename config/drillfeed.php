<?php

return [
    'ingest_token' => env('DRILLFEED_INGEST_TOKEN'),
    'api_rate_limit' => env('DRILLFEED_API_RATE_LIMIT', 60),
    'allowed_sources' => array_filter(array_map('trim', explode(',', env('DRILLFEED_ALLOWED_SOURCES', '')))),
    'min_quality_score' => (int) env('DRILLFEED_MIN_QUALITY_SCORE', 0),
];
