<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CommodityFilter from '@/components/CommodityFilter.vue';
import JurisdictionFilter from '@/components/JurisdictionFilter.vue';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import type { PaginatedMiningArticles } from '@/types';

interface Props {
  articles: PaginatedMiningArticles;
  commodities: { id: number; name: string; slug: string }[];
  jurisdictions: { id: number; name: string; slug: string }[];
  filters: {
    commodity?: string;
    company?: string;
    jurisdiction?: string;
    category?: string;
    search?: string;
  };
}

defineProps<Props>();
</script>

<template>
  <Head title="Mining Articles — OreWire" />

  <div class="min-h-screen bg-ore-deep">
    <SiteHeader />

    <main class="mx-auto max-w-[1400px] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
      <h1 class="mb-6 font-serif text-3xl font-bold text-ore-bright sm:text-4xl">
        Articles
      </h1>

      <div v-if="commodities.length || jurisdictions.length" class="mb-8 space-y-3">
        <CommodityFilter :commodities="commodities" :current="filters.commodity" :filters="filters" />
        <JurisdictionFilter :jurisdictions="jurisdictions" :current="filters.jurisdiction" :filters="filters" />
      </div>

      <div class="grid gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
        <MiningArticleCard
          v-for="article in articles.data"
          :key="article.id"
          :article="article"
        />
      </div>

      <div v-if="articles.last_page > 1" class="mt-10 flex justify-center gap-1">
        <Link
          v-for="link in articles.links.filter(l => l.url)"
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

      <p v-if="articles.data.length === 0" class="py-16 text-center text-ore-dim">
        No articles found.
      </p>
    </main>

    <SiteFooter />
  </div>
</template>
