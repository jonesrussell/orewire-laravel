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
    class="block rounded-lg border border-zinc-700 bg-zinc-800/50 p-4 transition-all hover:bg-zinc-800 dark:border-zinc-700 dark:bg-zinc-800/50"
  >
    <div class="flex items-baseline justify-between">
      <span v-if="drillResult.commodity" class="text-sm font-medium text-zinc-200">
        {{ drillResult.commodity.name }}
      </span>
      <span v-if="drillResult.grade" class="text-lg font-semibold text-amber-400">
        {{ drillResult.grade }} {{ drillResult.unit || 'g/t' }}
      </span>
    </div>
    <p v-if="drillResult.mining_article" class="mt-1 line-clamp-1 text-xs text-zinc-400">
      {{ drillResult.mining_article.title }}
    </p>
    <span v-if="drillResult.hole_id" class="mt-2 inline-block text-xs text-zinc-500">
      {{ drillResult.hole_id }}
      <span v-if="drillResult.intercept_m"> · {{ drillResult.intercept_m }}m</span>
    </span>
  </Link>
</template>
