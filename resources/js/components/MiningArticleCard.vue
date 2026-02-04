<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Calendar } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { MiningArticle } from '@/types';

interface Props {
  article: MiningArticle;
}

const props = defineProps<Props>();

const formattedDate = props.article.published_at
  ? new Date(props.article.published_at).toLocaleDateString('en-CA', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    })
  : '';
</script>

<template>
  <Link :href="`/articles/${article.slug}`">
    <Card
      class="h-full border-zinc-700 bg-zinc-800/50 transition-all hover:bg-zinc-800 hover:shadow-lg dark:border-zinc-700 dark:bg-zinc-800/50"
    >
      <img
        v-if="article.image_url"
        :src="article.image_url"
        :alt="article.title"
        class="h-48 w-full object-cover"
      />

      <CardHeader>
        <div class="mb-2 flex items-center justify-between text-xs">
          <span v-if="article.news_source" class="text-zinc-400">
            {{ article.news_source.name }}
          </span>
          <div class="flex items-center gap-1 text-zinc-400">
            <Calendar class="size-3" />
            {{ formattedDate }}
          </div>
        </div>

        <CardTitle class="line-clamp-2 text-zinc-100 dark:text-zinc-100">
          {{ article.title }}
        </CardTitle>
      </CardHeader>

      <CardContent>
        <p v-if="article.excerpt" class="mb-4 line-clamp-3 text-sm text-zinc-400">
          {{ article.excerpt }}
        </p>

        <div v-if="article.commodities?.length || article.mining_categories?.length" class="flex flex-wrap gap-2">
          <Badge
            v-for="commodity in (article.commodities || []).slice(0, 2)"
            :key="commodity.id"
            variant="secondary"
            class="bg-zinc-700 text-xs text-zinc-300"
          >
            {{ commodity.name }}
          </Badge>
          <Badge
            v-for="cat in (article.mining_categories || []).slice(0, 2)"
            :key="cat.id"
            variant="secondary"
            class="bg-zinc-700 text-xs text-zinc-300"
          >
            {{ cat.name }}
          </Badge>
        </div>
      </CardContent>
    </Card>
  </Link>
</template>
