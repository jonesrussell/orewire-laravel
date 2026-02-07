<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import DrillResultCard from '@/components/DrillResultCard.vue';
import type { PaginatedDrillResults } from '@/types';

interface Props {
  drillResults: PaginatedDrillResults;
  commodities?: { id: number; name: string; slug: string }[];
  filters: { commodity?: string };
}

defineProps<Props>();

const setCommodityFilter = (slug: string | null) => {
  router.get('/drill-results', { commodity: slug || undefined }, { preserveState: true });
};
</script>

<template>
  <Head title="Drill Results - OreWire" />

  <div class="min-h-screen bg-zinc-900 dark:bg-zinc-950">
    <header class="sticky top-0 z-50 border-b border-zinc-800 bg-zinc-950 dark:border-zinc-950">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <Link href="/" class="flex shrink-0 items-center gap-2 text-xl font-semibold text-zinc-100">
            OreWire
          </Link>

          <nav class="flex items-center gap-6">
            <Link href="/" class="text-sm text-zinc-300 hover:text-white">Home</Link>
            <Link href="/articles" class="text-sm text-zinc-300 hover:text-white">Articles</Link>
            <Link href="/drill-results" class="text-sm font-medium text-amber-500">Drill Results</Link>
          </nav>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <h1 class="mb-6 text-3xl font-bold text-zinc-100">Drill Results</h1>

      <div v-if="commodities?.length" class="mb-6 flex flex-wrap gap-2">
        <span class="text-sm text-zinc-500">Commodity:</span>
        <button
          type="button"
          class="rounded px-3 py-1 text-sm transition-colors"
          :class="!filters.commodity ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
          @click="setCommodityFilter(null)"
        >
          All
        </button>
        <button
          v-for="c in commodities"
          :key="c.id"
          type="button"
          class="rounded px-3 py-1 text-sm transition-colors"
          :class="filters.commodity === c.slug ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
          @click="setCommodityFilter(c.slug)"
        >
          {{ c.name }}
        </button>
      </div>

      <div class="space-y-4">
        <DrillResultCard
          v-for="drill in drillResults.data"
          :key="drill.id"
          :drill-result="drill"
        />
      </div>

      <div v-if="drillResults.last_page > 1" class="mt-8 flex justify-center gap-2">
        <Link
          v-for="link in drillResults.links.filter(l => l.url)"
          :key="link.label"
          :href="link.url!"
          class="rounded px-4 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-zinc-700 text-white': link.active }"
        >
          {{ link.label.replace('&laquo;', '‹').replace('&raquo;', '›') }}
        </Link>
      </div>

      <p v-if="drillResults.data.length === 0" class="py-12 text-zinc-500">
        No drill results found.
      </p>
    </main>
  </div>
</template>
