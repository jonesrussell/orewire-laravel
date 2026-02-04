<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import type { Company, PaginatedMiningArticles } from '@/types';

interface Props {
  company: Company;
  articles: PaginatedMiningArticles;
}

defineProps<Props>();
</script>

<template>
  <Head :title="`${company.name} - Drillfeed`" />

  <div class="min-h-screen bg-zinc-900 dark:bg-zinc-950">
    <header class="border-b border-zinc-800 bg-zinc-950 dark:border-zinc-950">
      <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <Link href="/articles" class="text-sm text-zinc-400 hover:text-white">
          ← Back to Articles
        </Link>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <h1 class="mb-2 text-3xl font-bold text-zinc-100">
        {{ company.name }}
      </h1>
      <p v-if="company.symbol" class="mb-8 text-zinc-500">
        {{ company.symbol }}
      </p>

      <h2 class="mb-4 text-xl font-semibold text-zinc-100">Articles</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <MiningArticleCard
          v-for="article in articles.data"
          :key="article.id"
          :article="article"
        />
      </div>

      <div v-if="articles.last_page > 1" class="mt-8 flex justify-center gap-2">
        <Link
          v-for="link in articles.links.filter(l => l.url)"
          :key="link.label"
          :href="link.url!"
          class="rounded px-4 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-zinc-700 text-white': link.active }"
        >
          {{ link.label.replace('&laquo;', '‹').replace('&raquo;', '›') }}
        </Link>
      </div>

      <p v-if="articles.data.length === 0" class="py-12 text-zinc-500">
        No articles for this company.
      </p>
    </main>
  </div>
</template>
