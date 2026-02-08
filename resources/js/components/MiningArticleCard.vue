<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { MiningArticle } from '@/types';

interface Props {
  article: MiningArticle;
  showImage?: boolean;
  compact?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showImage: true,
  compact: false,
});

const formattedDate = props.article.published_at
  ? new Date(props.article.published_at).toLocaleDateString('en-CA', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    })
  : '';
</script>

<template>
  <Link :href="`/articles/${article.slug}`" class="group block">
    <article class="h-full transition-colors">
      <div
        v-if="article.image_url && showImage && !compact"
        class="mb-3 overflow-hidden rounded"
      >
        <img
          :src="article.image_url"
          :alt="article.title"
          class="h-40 w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
          loading="lazy"
        />
      </div>

      <div class="mb-1.5 flex items-center gap-2 text-[11px] uppercase tracking-wider">
        <span v-if="article.news_source" class="font-medium text-ore-copper">
          {{ article.news_source.name }}
        </span>
        <span v-if="article.news_source && formattedDate" class="text-ore-dim">&middot;</span>
        <time v-if="formattedDate" class="text-ore-dim">{{ formattedDate }}</time>
      </div>

      <h3
        class="font-serif font-semibold leading-snug text-ore-bright transition-colors group-hover:text-ore-copper-light"
        :class="compact ? 'text-[0.9375rem]' : 'text-lg'"
      >
        <span class="line-clamp-2">{{ article.title }}</span>
      </h3>

      <p
        v-if="article.excerpt && !compact"
        class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-ore-mid"
      >
        {{ article.excerpt }}
      </p>

      <div
        v-if="(article.commodities?.length || article.mining_categories?.length) && !compact"
        class="mt-2.5 flex flex-wrap gap-1.5"
      >
        <span
          v-for="commodity in (article.commodities || []).slice(0, 2)"
          :key="commodity.id"
          class="rounded bg-ore-raised px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
        >
          {{ commodity.name }}
        </span>
        <span
          v-for="cat in (article.mining_categories || []).slice(0, 2)"
          :key="cat.id"
          class="rounded bg-ore-raised px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-ore-mid"
        >
          {{ cat.name }}
        </span>
      </div>
    </article>
  </Link>
</template>
