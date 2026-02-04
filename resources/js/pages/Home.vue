<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { Search, Menu } from 'lucide-vue-next';
import { ref } from 'vue';
import CompanyCard from '@/components/CompanyCard.vue';
import DrillResultCard from '@/components/DrillResultCard.vue';
import MetalPricesWidget from '@/components/MetalPricesWidget.vue';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import { Input } from '@/components/ui/input';
import type { MiningArticle, DrillResult, Company } from '@/types';

interface Props {
  latestArticles: MiningArticle[];
  latestDrillResults: DrillResult[];
  financings: MiningArticle[];
  trendingCompanies: (Company & { mining_articles_count?: number })[];
}

defineProps<Props>();

const searchQuery = ref('');
const showMobileMenu = ref(false);

const performSearch = () => {
  router.get('/articles', { search: searchQuery.value }, { preserveState: true });
};
</script>

<template>
  <Head title="Drillfeed - Mining News & Drill Results" />

  <div class="min-h-screen bg-zinc-900 dark:bg-zinc-950">
    <header class="sticky top-0 z-50 border-b border-zinc-800 bg-zinc-950 dark:border-zinc-800 dark:bg-zinc-950">
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
          <div class="mb-4">
            <Input
              v-model="searchQuery"
              type="search"
              placeholder="Search..."
              class="w-full border-zinc-700 bg-zinc-900 text-white"
              @keyup.enter="performSearch"
            />
          </div>
          <nav class="flex flex-col gap-2">
            <Link href="/" class="rounded px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white">Home</Link>
            <Link href="/articles" class="rounded px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white">Articles</Link>
            <Link href="/drill-results" class="rounded px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white">Drill Results</Link>
          </nav>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-8">
          <section>
            <h2 class="mb-4 text-xl font-semibold text-zinc-100">Latest Mining Articles</h2>
            <div v-if="latestArticles.length" class="grid gap-4 sm:grid-cols-2">
              <MiningArticleCard v-for="article in latestArticles" :key="article.id" :article="article" />
            </div>
            <p v-else class="text-zinc-500">No articles yet.</p>
            <Link href="/articles" class="mt-4 inline-block text-sm text-amber-500 hover:text-amber-400">
              View all articles →
            </Link>
          </section>

          <section>
            <h2 class="mb-4 text-xl font-semibold text-zinc-100">Latest Drill Results</h2>
            <div v-if="latestDrillResults.length" class="space-y-3">
              <DrillResultCard v-for="drill in latestDrillResults" :key="drill.id" :drill-result="drill" />
            </div>
            <p v-else class="text-zinc-500">No drill results yet.</p>
            <Link href="/drill-results" class="mt-4 inline-block text-sm text-amber-500 hover:text-amber-400">
              View all drill results →
            </Link>
          </section>

          <section v-if="financings.length">
            <h2 class="mb-4 text-xl font-semibold text-zinc-100">Financings</h2>
            <div class="grid gap-4 sm:grid-cols-2">
              <MiningArticleCard v-for="article in financings" :key="article.id" :article="article" />
            </div>
          </section>
        </div>

        <aside class="space-y-6">
          <MetalPricesWidget />

          <section>
            <h2 class="mb-4 text-xl font-semibold text-zinc-100">Trending Companies</h2>
            <div v-if="trendingCompanies.length" class="space-y-3">
              <CompanyCard
                v-for="company in trendingCompanies"
                :key="company.id"
                :company="company"
                :article-count="company.mining_articles_count"
              />
            </div>
            <p v-else class="text-zinc-500">No companies yet.</p>
          </section>
        </aside>
      </div>
    </main>
  </div>
</template>
