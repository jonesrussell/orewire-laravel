<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CommodityFilter from '@/components/CommodityFilter.vue';
import DrillResultCard from '@/components/DrillResultCard.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import type { PaginatedDrillResults } from '@/types';

interface Props {
  drillResults: PaginatedDrillResults;
  commodities?: { id: number; name: string; slug: string }[];
  filters: { commodity?: string };
}

defineProps<Props>();
</script>

<template>
  <Head title="Drill Results — OreWire" />

  <div class="min-h-screen bg-ore-deep">
    <SiteHeader />

    <main class="mx-auto max-w-[1400px] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
      <h1 class="mb-6 font-serif text-3xl font-bold text-ore-bright sm:text-4xl">
        Drill Results
      </h1>

      <div v-if="commodities?.length" class="mb-8">
        <CommodityFilter
          :commodities="commodities"
          :current="filters.commodity"
          :filters="filters"
          base-url="/drill-results"
        />
      </div>

      <!-- Data table header -->
      <div
        v-if="drillResults.data.length"
        class="mb-1 hidden items-center gap-4 border-b border-ore-line px-3 pb-2 text-[10px] font-semibold uppercase tracking-wider text-ore-dim md:flex"
      >
        <span class="w-14">Comm.</span>
        <span class="w-24 text-right">Grade</span>
        <span class="w-16 text-right">Length</span>
        <span class="w-24">Hole ID</span>
        <span class="flex-1">Article</span>
      </div>

      <div class="rounded border border-ore-line bg-ore-surface/50 p-3">
        <DrillResultCard
          v-for="drill in drillResults.data"
          :key="drill.id"
          :drill-result="drill"
        />
      </div>

      <div v-if="drillResults.last_page > 1" class="mt-10 flex justify-center gap-1">
        <Link
          v-for="link in drillResults.links.filter(l => l.url)"
          :key="link.label"
          :href="link.url!"
          class="rounded px-3.5 py-2 text-sm font-medium transition-colors"
          :class="link.active
            ? 'bg-ore-copper text-ore-deep'
            : 'text-ore-mid hover:bg-ore-raised hover:text-ore-bright'"
        >
          {{ link.label.replace('&laquo;', '\u2039').replace('&raquo;', '\u203A') }}
        </Link>
      </div>

      <p v-if="drillResults.data.length === 0" class="py-16 text-center text-ore-dim">
        No drill results found.
      </p>
    </main>

    <SiteFooter />
  </div>
</template>
