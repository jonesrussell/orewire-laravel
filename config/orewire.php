<?php

return [
    'ingest_token' => env('OREWIRE_INGEST_TOKEN'),
    'api_rate_limit' => env('OREWIRE_API_RATE_LIMIT', 60),
    'allowed_sources' => array_filter(array_map('trim', explode(',', env('OREWIRE_ALLOWED_SOURCES', '')))),
    'min_quality_score' => (int) env('OREWIRE_MIN_QUALITY_SCORE', 0),
];
