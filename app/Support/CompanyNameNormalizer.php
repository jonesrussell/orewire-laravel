<?php

namespace App\Support;

use Illuminate\Support\Str;

class CompanyNameNormalizer
{
    private const SUFFIXES = [
        'Inc', 'Inc.', 'Incorporated',
        'Corp', 'Corp.', 'Corporation',
        'Ltd', 'Ltd.', 'Limited',
        'Co', 'Co.', 'Company',
        'PLC', 'plc',
        'LLC', 'L.L.C.',
        'LP', 'L.P.',
        'GmbH',
        'SA', 'S.A.',
        'NV', 'N.V.',
    ];

    /**
     * Normalize a company name for matching: strip suffixes, trim, lowercase.
     */
    public static function normalize(string $name): string
    {
        $normalized = trim($name);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        foreach (self::SUFFIXES as $suffix) {
            $pattern = '/\s+'.preg_quote($suffix, '/').'\.?\s*$/i';
            $normalized = preg_replace($pattern, '', $normalized);
            $normalized = trim($normalized);
        }

        return Str::lower($normalized);
    }

    /**
     * Generate a slug from a normalized company name.
     */
    public static function slug(string $name): string
    {
        return Str::slug(self::normalize($name), '-');
    }
}
