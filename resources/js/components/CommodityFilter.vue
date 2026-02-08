<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { Commodity } from '@/types';

interface Props {
  commodities?: Commodity[];
  current?: string;
  filters?: Record<string, string | undefined>;
  baseUrl?: string;
}

const props = withDefaults(defineProps<Props>(), {
  commodities: () => [],
  current: '',
  filters: () => ({}),
  baseUrl: '/articles',
});

const setFilter = (slug: string | null) => {
  router.get(props.baseUrl, { ...props.filters, commodity: slug || undefined }, { preserveState: true });
};
</script>

<template>
  <div class="flex flex-wrap items-center gap-1.5">
    <span class="mr-1 text-[11px] font-medium uppercase tracking-wider text-ore-dim">Commodity</span>
    <button
      type="button"
      class="rounded px-2.5 py-1 text-xs font-medium transition-colors"
      :class="!current
        ? 'bg-ore-copper text-ore-deep'
        : 'bg-ore-raised text-ore-mid hover:bg-ore-hover hover:text-ore-bright'"
      @click="setFilter(null)"
    >
      All
    </button>
    <button
      v-for="c in commodities"
      :key="c.id"
      type="button"
      class="rounded px-2.5 py-1 text-xs font-medium transition-colors"
      :class="current === c.slug
        ? 'bg-ore-copper text-ore-deep'
        : 'bg-ore-raised text-ore-mid hover:bg-ore-hover hover:text-ore-bright'"
      @click="setFilter(c.slug)"
    >
      {{ c.name }}
    </button>
  </div>
</template>
