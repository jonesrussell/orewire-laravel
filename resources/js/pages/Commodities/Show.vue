<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import type { Commodity, PaginatedMiningArticles } from '@/types';

interface Props {
  commodity: Commodity;
  articles: PaginatedMiningArticles;
}

defineProps<Props>();
</script>

<template>
  <Head :title="`${commodity.name} — OreWire`" />

  <div class="min-h-screen bg-ore-deep">
    <SiteHeader />

    <main class="mx-auto max-w-[1400px] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
      <Link
        href="/articles"
        class="mb-6 inline-flex items-center gap-1.5 text-sm text-ore-mid transition-colors hover:text-ore-copper"
      >
        <ArrowLeft class="size-3.5" />
        Back to Articles
      </Link>

      <h1 class="mb-1 font-serif text-3xl font-bold text-ore-bright sm:text-4xl">
        {{ commodity.name }}
      </h1>
      <p v-if="commodity.symbol" class="mb-8 text-sm text-ore-dim">
        {{ commodity.symbol }}
      </p>

      <h2 class="section-label mb-5">Articles</h2>
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
        No articles for this commodity.
      </p>
    </main>

    <SiteFooter />
  </div>
</template>
