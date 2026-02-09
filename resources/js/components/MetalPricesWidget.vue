<script setup lang="ts">
import type { CommodityPrice } from '@/types';

defineProps<{
  prices: CommodityPrice[];
}>();

function formatPrice(price: string | number): string {
  const num = typeof price === 'string' ? parseFloat(price) : price;
  if (num >= 1000) {
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  if (num >= 1) {
    return num.toFixed(2);
  }
  return num.toFixed(4);
}

function formatChange(change: string | null): string {
  if (!change) return '';
  const num = parseFloat(change);
  const sign = num >= 0 ? '+' : '';
  return `${sign}${num.toFixed(2)}%`;
}

function changeClass(change: string | null): string {
  if (!change) return 'text-ore-dim';
  return parseFloat(change) >= 0 ? 'text-ore-green' : 'text-ore-red';
}

function timeAgo(fetched: string | null): string {
  if (!fetched) return '';
  const diff = Date.now() - new Date(fetched).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return 'just now';
  if (mins < 60) return `${mins}m ago`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}h ago`;
  return `${Math.floor(hours / 24)}d ago`;
}
</script>

<template>
  <div class="rounded border border-ore-line bg-ore-surface/50 p-4">
    <h3 class="section-label mb-3">Metal Prices</h3>

    <div v-if="prices.length" class="space-y-2">
      <div v-for="item in prices" :key="item.symbol" class="flex items-center justify-between text-sm">
        <span class="text-ore-mid">{{ item.name }}</span>
        <span class="flex items-center gap-2">
          <span class="font-mono text-xs text-ore-bright">${{ formatPrice(item.price_usd) }}</span>
          <span v-if="item.change_24h_percent" class="font-mono text-[10px]" :class="changeClass(item.change_24h_percent)">
            {{ formatChange(item.change_24h_percent) }}
          </span>
        </span>
      </div>
    </div>

    <div v-else class="space-y-2">
      <div v-for="name in ['Gold', 'Silver', 'Copper']" :key="name" class="flex items-center justify-between text-sm">
        <span class="text-ore-mid">{{ name }}</span>
        <span class="font-mono text-xs text-ore-dim">&mdash;</span>
      </div>
    </div>

    <p v-if="prices.length" class="mt-3 text-center text-[10px] uppercase tracking-wider text-ore-dim">
      Updated {{ timeAgo(prices[0]?.fetched_at) }}
    </p>
    <p v-else class="mt-3 text-center text-[10px] uppercase tracking-wider text-ore-dim">
      Prices unavailable
    </p>
  </div>
</template>
