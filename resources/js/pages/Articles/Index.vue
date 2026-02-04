<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Menu } from 'lucide-vue-next';
import { ref } from 'vue';
import CommodityFilter from '@/components/CommodityFilter.vue';
import JurisdictionFilter from '@/components/JurisdictionFilter.vue';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import { Input } from '@/components/ui/input';
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

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const showMobileMenu = ref(false);

const performSearch = () => {
  router.get('/articles', { ...props.filters, search: searchQuery.value }, { preserveState: true });
};
</script>

<template>
  <Head title="Mining Articles - Drillfeed" />

  <div class="min-h-screen bg-zinc-900 dark:bg-zinc-950">
    <header class="sticky top-0 z-50 border-b border-zinc-800 bg-zinc-950 dark:border-zinc-950">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <Link href="/" class="flex shrink-0 items-center gap-2 text-xl font-semibold text-zinc-100">
            Drillfeed
          </Link>

          <nav class="hidden items-center gap-6 md:flex">
            <Link href="/" class="text-sm text-zinc-300 hover:text-white">Home</Link>
            <Link href="/articles" class="text-sm text-zinc-300 hover:text-white">Articles</Link>
            <Link href="/drill-results" class="text-sm text-zinc-300 hover:text-white">Drill Results</Link>
          </nav>

          <div class="flex items-center gap-4">
            <div class="relative hidden md:block">
              <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-500" />
              <Input
                v-model="searchQuery"
                type="search"
                placeholder="Search..."
                class="w-64 border-zinc-700 bg-zinc-900 pl-10 text-white placeholder:text-zinc-500"
                @keyup.enter="performSearch"
              />
            </div>
            <button
              type="button"
              class="text-zinc-400 hover:text-white md:hidden"
              @click="showMobileMenu = !showMobileMenu"
            >
              <Menu class="size-5" />
            </button>
          </div>
        </div>

        <div v-if="showMobileMenu" class="border-t border-zinc-800 py-4 md:hidden">
          <Input
            v-model="searchQuery"
            type="search"
            placeholder="Search..."
            class="mb-4 w-full border-zinc-700 bg-zinc-900 text-white"
            @keyup.enter="performSearch"
          />
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="mb-6 flex flex-wrap gap-4">
        <CommodityFilter :commodities="commodities" :current="filters.commodity" :filters="filters" />
        <JurisdictionFilter :jurisdictions="jurisdictions" :current="filters.jurisdiction" :filters="filters" />
      </div>

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

      <p v-if="articles.data.length === 0" class="py-12 text-center text-zinc-500">
        No articles found.
      </p>
    </main>
  </div>
</template>
