<?php

namespace App\Services\Resolvers;

use App\Models\NewsSource;
use Illuminate\Support\Str;

class NewsSourceResolver
{
    public function resolve(string $sourceUrl): NewsSource
    {
        $slug = $this->slugFromUrl($sourceUrl);

        return NewsSource::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $this->nameFromUrl($sourceUrl),
                'url' => $sourceUrl ?: 'https://unknown.example.com',
                'is_active' => true,
            ]
        );
    }

    private function slugFromUrl(string $url): string
    {
        if (empty($url)) {
            return 'unknown';
        }

        try {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? '';
            $host = preg_replace('/^www\./', '', $host);
            $parts = explode('.', $host);
            $name = $parts[0] ?? $host;

            return Str::slug($name);
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    private function nameFromUrl(string $url): string
    {
        if (empty($url)) {
            return 'Unknown Source';
        }

        try {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? '';
            $host = preg_replace('/^www\./', '', $host);
            $parts = explode('.', $host);
            $name = $parts[0] ?? $host;

            return Str::title($name);
        } catch (\Throwable) {
            return 'Unknown Source';
        }
    }
}
