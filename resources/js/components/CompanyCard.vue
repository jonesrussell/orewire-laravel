<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Company } from '@/types';

interface Props {
  company: Company;
  articleCount?: number;
}

defineProps<Props>();
</script>

<template>
  <Link
    :href="`/companies/${company.slug}`"
    class="block rounded-lg border border-zinc-700 bg-zinc-800/50 p-4 transition-all hover:bg-zinc-800 dark:border-zinc-700 dark:bg-zinc-800/50"
  >
    <div class="font-medium text-zinc-200 dark:text-zinc-200">
      {{ company.name }}
    </div>
    <div v-if="company.symbol" class="text-xs text-zinc-500 dark:text-zinc-500">
      {{ company.symbol }}
    </div>
    <div v-if="articleCount !== undefined" class="mt-2 text-xs text-zinc-400">
      {{ articleCount }} article{{ articleCount !== 1 ? 's' : '' }}
    </div>
  </Link>
</template>
