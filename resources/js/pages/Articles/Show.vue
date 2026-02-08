<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, User, ExternalLink, ArrowLeft } from 'lucide-vue-next';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import type { MiningArticle } from '@/types';

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
  <Head :title="`${article.title} — OreWire`" />

  <div class="min-h-screen bg-ore-deep">
    <SiteHeader />

    <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
      <Link
        href="/articles"
        class="mb-6 inline-flex items-center gap-1.5 text-sm text-ore-mid transition-colors hover:text-ore-copper"
      >
        <ArrowLeft class="size-3.5" />
        Back to Articles
      </Link>

      <article>
        <header class="mb-8">
          <div class="mb-3 flex flex-wrap items-center gap-3 text-[11px] uppercase tracking-wider">
            <span v-if="article.news_source" class="font-medium text-ore-copper">
              {{ article.news_source.name }}
            </span>
            <div class="flex items-center gap-1.5 text-ore-dim">
              <Calendar class="size-3" />
              {{ formattedDate }}
            </div>
            <div v-if="article.author" class="flex items-center gap-1.5 text-ore-dim">
              <User class="size-3" />
              {{ article.author }}
            </div>
          </div>

          <h1 class="font-serif text-3xl font-bold leading-tight text-ore-bright sm:text-4xl lg:text-[2.75rem]">
            {{ article.title }}
          </h1>

          <div
            v-if="article.commodities?.length || article.companies?.length || article.mining_categories?.length"
            class="mt-5 flex flex-wrap gap-1.5"
          >
            <span
              v-for="c in article.commodities"
              :key="'c-'+c.id"
              class="rounded bg-ore-raised px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
            >
              {{ c.name }}
            </span>
            <span
              v-for="co in article.companies"
              :key="'co-'+co.id"
              class="rounded bg-ore-raised px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
            >
              {{ co.name }}
            </span>
            <span
              v-for="cat in article.mining_categories"
              :key="'cat-'+cat.id"
              class="rounded bg-ore-raised px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
            >
              {{ cat.name }}
            </span>
          </div>

          <a
            v-if="article.url"
            :href="article.url"
            target="_blank"
            rel="noopener"
            class="mt-4 inline-flex items-center gap-1.5 text-sm text-ore-copper transition-colors hover:text-ore-copper-light"
          >
            <ExternalLink class="size-3.5" />
            Read original
          </a>
        </header>

        <!-- Drill results -->
        <div
          v-if="article.drill_results?.length"
          class="mb-8 rounded border border-ore-line bg-ore-surface/50 p-4"
        >
          <h2 class="section-label mb-3">Drill Results</h2>
          <div class="space-y-1">
            <div
              v-for="drill in article.drill_results"
              :key="drill.id"
              class="flex items-center justify-between rounded px-3 py-2 text-sm odd:bg-ore-raised/50"
            >
              <span class="text-ore-mid">
                {{ drill.commodity?.name || 'N/A' }}
                <span v-if="drill.hole_id" class="ml-1 text-ore-dim">({{ drill.hole_id }})</span>
              </span>
              <span class="font-mono font-semibold tabular-nums text-ore-gold">
                {{ drill.grade }} {{ drill.unit || 'g/t' }}
                <span v-if="drill.intercept_m" class="ml-1 text-ore-dim">/ {{ drill.intercept_m }}m</span>
              </span>
            </div>
          </div>
        </div>

        <!-- Article body -->
        <div
          class="prose-ore max-w-none"
          v-html="article.content || article.excerpt || ''"
        />

        <!-- Related articles -->
        <section v-if="relatedArticles.length" class="mt-14">
          <h2 class="section-label mb-5">Related Articles</h2>
          <div class="grid gap-x-6 gap-y-6 sm:grid-cols-2">
            <MiningArticleCard
              v-for="rel in relatedArticles"
              :key="rel.id"
              :article="rel"
            />
          </div>
        </section>
      </article>
    </main>

    <SiteFooter />
  </div>
</template>
