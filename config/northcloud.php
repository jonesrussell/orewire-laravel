<?php

return [
    'migrations' => [
        'enabled' => false,
    ],

    'redis' => [
        'connection' => env('NORTHCLOUD_REDIS_CONNECTION', 'northcloud'),
        'channels' => ['articles:mining'],
    ],

    'quality' => [
        'min_score' => (int) env('NORTHCLOUD_MIN_QUALITY_SCORE', 0),
        'enabled' => false,
    ],

    'models' => [
        'article' => \App\Models\MiningArticle::class,
        'news_source' => \App\Models\NewsSource::class,
        'tag' => \App\Models\Tag::class,
    ],

    'processors' => [
        \App\Processing\MiningArticleProcessor::class,
    ],

    'processing' => [
        'sync' => true,
    ],

    'content' => [
        'allowed_tags' => ['p', 'br', 'a', 'strong', 'em', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    ],

    'tags' => [
        'default_type' => 'topic',
        'auto_create' => true,
        'allowed' => [],
    ],
];
