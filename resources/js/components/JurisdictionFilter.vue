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
  <div class="flex flex-wrap items-center gap-1.5">
    <span class="mr-1 text-[11px] font-medium uppercase tracking-wider text-ore-dim">Region</span>
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
      v-for="j in jurisdictions"
      :key="j.id"
      type="button"
      class="rounded px-2.5 py-1 text-xs font-medium transition-colors"
      :class="current === j.slug
        ? 'bg-ore-copper text-ore-deep'
        : 'bg-ore-raised text-ore-mid hover:bg-ore-hover hover:text-ore-bright'"
      @click="setFilter(j.slug)"
    >
      {{ j.name }}
    </button>
  </div>
</template>
