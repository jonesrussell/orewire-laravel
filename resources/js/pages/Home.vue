<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CompanyCard from '@/components/CompanyCard.vue';
import DrillResultCard from '@/components/DrillResultCard.vue';
import MetalPricesWidget from '@/components/MetalPricesWidget.vue';
import MiningArticleCard from '@/components/MiningArticleCard.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import type { MiningArticle, DrillResult, Company } from '@/types';

interface Props {
  latestArticles: MiningArticle[];
  latestDrillResults: DrillResult[];
  financings: MiningArticle[];
  trendingCompanies: (Company & { mining_articles_count?: number })[];
}

const props = defineProps<Props>();

const featuredArticle = props.latestArticles[0] ?? null;
const gridArticles = props.latestArticles.slice(1);

const featuredDate = featuredArticle?.published_at
  ? new Date(featuredArticle.published_at).toLocaleDateString('en-CA', {
      month: 'long',
      day: 'numeric',
      year: 'numeric',
    })
  : '';
</script>

<template>
  <Head title="OreWire — Canadian Mining Intelligence" />

  <div class="min-h-screen bg-ore-deep">
    <SiteHeader />

    <!-- Wire ticker -->
    <div
      v-if="latestArticles.length >= 4"
      class="ticker-fade relative overflow-hidden border-b border-ore-line-faint bg-ore-base"
    >
      <div class="ticker-track">
        <div class="flex items-center gap-6 px-6 py-2">
          <span class="shrink-0 text-[10px] font-semibold uppercase tracking-[0.2em] text-ore-copper">
            The Wire
          </span>
          <template v-for="article in latestArticles" :key="article.id">
            <span class="text-ore-dim">&middot;</span>
            <Link
              :href="`/articles/${article.slug}`"
              class="shrink-0 text-[13px] text-ore-mid transition-colors hover:text-ore-bright"
            >
              {{ article.title }}
            </Link>
          </template>
        </div>
        <div class="flex items-center gap-6 px-6 py-2" aria-hidden="true">
          <span class="shrink-0 text-[10px] font-semibold uppercase tracking-[0.2em] text-ore-copper">
            The Wire
          </span>
          <template v-for="article in latestArticles" :key="'dup-' + article.id">
            <span class="text-ore-dim">&middot;</span>
            <span class="shrink-0 text-[13px] text-ore-mid">
              {{ article.title }}
            </span>
          </template>
        </div>
      </div>
    </div>

    <main class="mx-auto max-w-[1400px] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
      <div class="grid gap-10 lg:grid-cols-[1fr_320px] xl:grid-cols-[1fr_360px]">
        <!-- Main column -->
        <div class="min-w-0 space-y-10">
          <!-- Featured article -->
          <section v-if="featuredArticle">
            <Link :href="`/articles/${featuredArticle.slug}`" class="group block">
              <article>
                <div
                  v-if="featuredArticle.image_url"
                  class="mb-5 overflow-hidden rounded-lg"
                >
                  <img
                    :src="featuredArticle.image_url"
                    :alt="featuredArticle.title"
                    class="h-64 w-full object-cover transition-transform duration-700 group-hover:scale-[1.02] sm:h-80"
                  />
                </div>

                <div class="mb-2 flex items-center gap-2 text-[11px] uppercase tracking-wider">
                  <span v-if="featuredArticle.news_source" class="font-medium text-ore-copper">
                    {{ featuredArticle.news_source.name }}
                  </span>
                  <span v-if="featuredArticle.news_source && featuredDate" class="text-ore-dim">&middot;</span>
                  <time v-if="featuredDate" class="text-ore-dim">{{ featuredDate }}</time>
                </div>

                <h2 class="font-serif text-3xl font-bold leading-tight text-ore-bright transition-colors group-hover:text-ore-copper-light sm:text-4xl">
                  {{ featuredArticle.title }}
                </h2>

                <p
                  v-if="featuredArticle.excerpt"
                  class="mt-3 line-clamp-3 text-base leading-relaxed text-ore-mid"
                >
                  {{ featuredArticle.excerpt }}
                </p>

                <div
                  v-if="featuredArticle.commodities?.length || featuredArticle.mining_categories?.length"
                  class="mt-4 flex flex-wrap gap-1.5"
                >
                  <span
                    v-for="c in (featuredArticle.commodities || []).slice(0, 3)"
                    :key="c.id"
                    class="rounded bg-ore-raised px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
                  >
                    {{ c.name }}
                  </span>
                  <span
                    v-for="cat in (featuredArticle.mining_categories || []).slice(0, 2)"
                    :key="cat.id"
                    class="rounded bg-ore-raised px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
                  >
                    {{ cat.name }}
                  </span>
                </div>
              </article>
            </Link>
          </section>

          <!-- Latest articles grid -->
          <section v-if="gridArticles.length">
            <h2 class="section-label mb-5">Latest Articles</h2>
            <div class="grid gap-x-6 gap-y-6 sm:grid-cols-2">
              <MiningArticleCard
                v-for="article in gridArticles"
                :key="article.id"
                :article="article"
              />
            </div>
            <Link
              href="/articles"
              class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-ore-copper transition-colors hover:text-ore-copper-light"
            >
              View all articles
              <span aria-hidden="true">&rarr;</span>
            </Link>
          </section>

          <!-- Financings -->
          <section v-if="financings.length">
            <h2 class="section-label mb-5">Financings</h2>
            <div class="grid gap-x-6 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
              <MiningArticleCard
                v-for="article in financings"
                :key="article.id"
                :article="article"
                :show-image="false"
                compact
              />
            </div>
          </section>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-8">
          <!-- Drill results -->
          <section>
            <h2 class="section-label mb-4">Drill Results</h2>
            <div
              v-if="latestDrillResults.length"
              class="rounded border border-ore-line bg-ore-surface/50 p-3"
            >
              <DrillResultCard
                v-for="drill in latestDrillResults"
                :key="drill.id"
                :drill-result="drill"
              />
            </div>
            <p v-else class="text-sm text-ore-dim">No drill results yet.</p>
            <Link
              href="/drill-results"
              class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-ore-copper transition-colors hover:text-ore-copper-light"
            >
              View all drill results
              <span aria-hidden="true">&rarr;</span>
            </Link>
          </section>

          <MetalPricesWidget />

          <!-- Trending companies -->
          <section>
            <h2 class="section-label mb-4">Trending Companies</h2>
            <div
              v-if="trendingCompanies.length"
              class="rounded border border-ore-line bg-ore-surface/50 p-3"
            >
              <CompanyCard
                v-for="company in trendingCompanies"
                :key="company.id"
                :company="company"
                :article-count="company.mining_articles_count"
              />
            </div>
            <p v-else class="text-sm text-ore-dim">No companies yet.</p>
          </section>
        </aside>
      </div>
    </main>

    <SiteFooter />
  </div>
</template>
