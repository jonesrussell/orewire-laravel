<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { DrillResult } from '@/types';

interface Props {
  drillResult: DrillResult;
}

defineProps<Props>();
</script>

<template>
  <Link
    v-if="drillResult.mining_article"
    :href="`/articles/${drillResult.mining_article.slug}`"
    class="group flex items-center gap-4 border-b border-ore-line-faint py-2.5 transition-colors last:border-0 hover:bg-ore-surface/50"
  >
    <span
      v-if="drillResult.commodity"
      class="w-14 shrink-0 text-[11px] font-semibold uppercase tracking-wider text-ore-mid"
    >
      {{ drillResult.commodity.symbol || drillResult.commodity.name.slice(0, 4) }}
    </span>

    <span
      v-if="drillResult.grade"
      class="w-24 shrink-0 text-right font-mono text-sm font-semibold tabular-nums text-ore-gold"
    >
      {{ drillResult.grade }} {{ drillResult.unit || 'g/t' }}
    </span>

    <span
      v-if="drillResult.intercept_m"
      class="w-16 shrink-0 text-right font-mono text-xs tabular-nums text-ore-dim"
    >
      {{ drillResult.intercept_m }}m
    </span>

    <span
      v-if="drillResult.hole_id"
      class="w-24 shrink-0 font-mono text-xs text-ore-dim"
    >
      {{ drillResult.hole_id }}
    </span>

    <span
      v-if="drillResult.mining_article"
      class="min-w-0 flex-1 truncate text-xs text-ore-mid transition-colors group-hover:text-ore-bright"
    >
      {{ drillResult.mining_article.title }}
    </span>
  </Link>
</template>
