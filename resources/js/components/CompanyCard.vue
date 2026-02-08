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
    class="group flex items-center justify-between border-b border-ore-line-faint py-2 transition-colors last:border-0 hover:bg-ore-surface/50"
  >
    <div class="min-w-0">
      <span class="text-sm font-medium text-ore-bright transition-colors group-hover:text-ore-copper-light">
        {{ company.name }}
      </span>
      <span v-if="company.symbol" class="ml-2 text-xs text-ore-dim">
        {{ company.symbol }}
      </span>
    </div>
    <span v-if="articleCount !== undefined" class="ml-3 shrink-0 text-xs tabular-nums text-ore-dim">
      {{ articleCount }}
    </span>
  </Link>
</template>
