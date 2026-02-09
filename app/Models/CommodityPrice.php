<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommodityPrice extends Model
{
    /** @use HasFactory<\Database\Factories\CommodityPriceFactory> */
    use HasFactory;

    protected $fillable = [
        'symbol',
        'name',
        'type',
        'price_usd',
        'previous_price_usd',
        'change_24h_percent',
        'fetched_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price_usd' => 'decimal:2',
            'previous_price_usd' => 'decimal:2',
            'change_24h_percent' => 'decimal:4',
            'fetched_at' => 'datetime',
        ];
    }

    public function scopeMetals(Builder $query): Builder
    {
        return $query->where('type', 'metal');
    }

    public function scopeCryptos(Builder $query): Builder
    {
        return $query->where('type', 'crypto');
    }
}
