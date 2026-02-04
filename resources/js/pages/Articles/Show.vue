<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { MiningArticle } from '@/types';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import { Badge } from '@/components/ui/badge';
import { Calendar, User, ExternalLink } from 'lucide-vue-next';

interface Props {
  article: MiningArticle;
  relatedArticles: MiningArticle[];
}

const props = defineProps<Props>();

const formattedDate = props.article.published_at
  ? new Date(props.article.published_at).toLocaleDateString('en-CA', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  : 'Not published';
</script>

<template>
  <Head :title="article.title" />

  <div class="min-h-screen bg-zinc-900 dark:bg-zinc-950">
    <header class="border-b border-zinc-800 bg-zinc-950 dark:border-zinc-800 dark:bg-zinc-950">
      <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
        <Link
          href="/articles"
          class="text-sm text-zinc-400 hover:text-white"
        >
          ← Back to Articles
        </Link>
      </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
      <article>
        <header class="mb-8">
          <h1 class="mb-4 text-4xl font-bold text-zinc-100 dark:text-zinc-100">
            {{ article.title }}
          </h1>

          <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-400">
            <div class="flex items-center gap-2">
              <Calendar class="size-4" />
              {{ formattedDate }}
            </div>

            <div v-if="article.author" class="flex items-center gap-2">
              <User class="size-4" />
              {{ article.author }}
            </div>

            <div v-if="article.news_source" class="flex items-center gap-2">
              <span>{{ article.news_source.name }}</span>
            </div>

            <a
              v-if="article.url"
              :href="article.url"
              target="_blank"
              rel="noopener"
              class="flex items-center gap-1 text-amber-500 hover:text-amber-400"
            >
              <ExternalLink class="size-4" />
              Original
            </a>
          </div>

          <div
            v-if="article.commodities?.length || article.companies?.length || article.mining_categories?.length"
            class="mt-4 flex flex-wrap gap-2"
          >
            <Badge
              v-for="c in article.commodities"
              :key="'c-'+c.id"
              variant="secondary"
              class="bg-zinc-700 text-zinc-300"
            >
              {{ c.name }}
            </Badge>
            <Badge
              v-for="co in article.companies"
              :key="'co-'+co.id"
              variant="secondary"
              class="bg-zinc-700 text-zinc-300"
            >
              {{ co.name }}
            </Badge>
            <Badge
              v-for="cat in article.mining_categories"
              :key="'cat-'+cat.id"
              variant="secondary"
              class="bg-zinc-700 text-zinc-300"
            >
              {{ cat.name }}
            </Badge>
          </div>
        </header>

        <div
          v-if="article.drill_results?.length"
          class="mb-8 rounded-lg border border-zinc-700 bg-zinc-800/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"
        >
          <h2 class="mb-3 text-lg font-semibold text-zinc-100">Drill Results</h2>
          <div class="space-y-2">
            <div
              v-for="drill in article.drill_results"
              :key="drill.id"
              class="flex justify-between rounded bg-zinc-800/80 px-3 py-2 text-sm"
            >
              <span class="text-zinc-300">
                {{ drill.commodity?.name || 'N/A' }}
                <span v-if="drill.hole_id" class="text-zinc-500"> ({{ drill.hole_id }})</span>
              </span>
              <span class="font-medium text-amber-400">
                {{ drill.grade }} {{ drill.unit || 'g/t' }}
                <span v-if="drill.intercept_m"> · {{ drill.intercept_m }}m</span>
              </span>
            </div>
          </div>
        </div>

        <div
          class="prose prose-invert max-w-none dark:prose-invert"
          v-html="article.content || article.excerpt || ''"
        />

        <section v-if="relatedArticles.length" class="mt-12">
          <h2 class="mb-4 text-xl font-semibold text-zinc-100">Related Articles</h2>
          <div class="grid gap-4 sm:grid-cols-2">
            <MiningArticleCard
              v-for="rel in relatedArticles"
              :key="rel.id"
              :article="rel"
            />
          </div>
        </section>
      </article>
    </main>
  </div>
</template>
