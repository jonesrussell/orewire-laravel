<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { Commodity } from '@/types';

interface Props {
  commodities?: Commodity[];
  current?: string;
  filters?: Record<string, string | undefined>;
}

const props = withDefaults(defineProps<Props>(), {
  commodities: () => [],
  current: '',
  filters: () => ({}),
});

const setFilter = (slug: string | null) => {
  router.get('/articles', { ...props.filters, commodity: slug || undefined }, { preserveState: true });
};
</script>

<template>
  <div class="flex flex-wrap items-center gap-2">
    <span class="text-sm text-zinc-500">Commodity:</span>
    <button
      type="button"
      class="rounded px-3 py-1 text-sm transition-colors"
      :class="!current ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
      @click="setFilter(null)"
    >
      All
    </button>
    <button
      v-for="c in commodities"
      :key="c.id"
      type="button"
      class="rounded px-3 py-1 text-sm transition-colors"
      :class="current === c.slug ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
      @click="setFilter(c.slug)"
    >
      {{ c.name }}
    </button>
  </div>
</template>
