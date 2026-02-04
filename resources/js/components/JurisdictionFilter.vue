<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { MiningJurisdiction } from '@/types';

interface Props {
  jurisdictions?: MiningJurisdiction[];
  current?: string;
  filters?: Record<string, string | undefined>;
}

const props = withDefaults(defineProps<Props>(), {
  jurisdictions: () => [],
  current: '',
  filters: () => ({}),
});

const setFilter = (slug: string | null) => {
  router.get('/articles', { ...props.filters, jurisdiction: slug || undefined }, { preserveState: true });
};
</script>

<template>
  <div class="flex flex-wrap items-center gap-2">
    <span class="text-sm text-zinc-500">Jurisdiction:</span>
    <button
      type="button"
      class="rounded px-3 py-1 text-sm transition-colors"
      :class="!current ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
      @click="setFilter(null)"
    >
      All
    </button>
    <button
      v-for="j in jurisdictions"
      :key="j.id"
      type="button"
      class="rounded px-3 py-1 text-sm transition-colors"
      :class="current === j.slug ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
      @click="setFilter(j.slug)"
    >
      {{ j.name }}
    </button>
  </div>
</template>
