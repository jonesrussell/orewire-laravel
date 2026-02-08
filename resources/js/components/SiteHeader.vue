<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search, Menu, X } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const searchQuery = ref('');
const showMobileMenu = ref(false);

const currentPath = computed(() => usePage().url);

const isActive = (path: string) => {
  if (path === '/') return currentPath.value === '/';
  return currentPath.value.startsWith(path);
};

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.get('/articles', { search: searchQuery.value }, { preserveState: true });
    showMobileMenu.value = false;
  }
};

const navItems = [
  { href: '/', label: 'Home' },
  { href: '/articles', label: 'Articles' },
  { href: '/drill-results', label: 'Drill Results' },
];
</script>

<template>
  <div class="h-[2px] bg-gradient-to-r from-transparent via-ore-copper to-transparent" />

  <header class="sticky top-0 z-50 border-b border-ore-line bg-ore-deep/95 backdrop-blur-sm">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="flex h-14 items-center justify-between lg:h-16">
        <Link href="/" class="group flex items-baseline gap-3">
          <span class="font-serif text-xl font-bold tracking-[0.04em] text-ore-bright lg:text-[1.375rem]">
            OREWIRE
          </span>
          <span class="hidden text-[10px] font-medium uppercase tracking-[0.2em] text-ore-dim xl:inline">
            Canadian mining intelligence
          </span>
        </Link>

        <nav class="hidden items-center gap-1 md:flex">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="rounded-md px-3 py-1.5 text-[13px] font-medium tracking-wide transition-colors"
            :class="isActive(item.href)
              ? 'text-ore-copper'
              : 'text-ore-mid hover:text-ore-bright'"
          >
            {{ item.label }}
          </Link>
        </nav>

        <div class="flex items-center gap-3">
          <div class="relative hidden lg:block">
            <Search class="pointer-events-none absolute left-3 top-1/2 size-3.5 -translate-y-1/2 text-ore-dim" />
            <input
              v-model="searchQuery"
              type="search"
              placeholder="Search articles..."
              class="h-8 w-56 rounded border border-ore-line bg-ore-surface pl-9 pr-3 text-sm text-ore-bright placeholder:text-ore-dim focus:border-ore-copper focus:outline-none focus:ring-1 focus:ring-ore-copper/30 xl:w-64"
              @keyup.enter="performSearch"
            />
          </div>

          <button
            type="button"
            class="rounded-md p-1.5 text-ore-mid hover:text-ore-bright md:hidden"
            @click="showMobileMenu = !showMobileMenu"
          >
            <X v-if="showMobileMenu" class="size-5" />
            <Menu v-else class="size-5" />
          </button>
        </div>
      </div>

      <div v-if="showMobileMenu" class="border-t border-ore-line pb-4 pt-3 md:hidden">
        <div class="relative mb-3">
          <Search class="pointer-events-none absolute left-3 top-1/2 size-3.5 -translate-y-1/2 text-ore-dim" />
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Search articles..."
            class="h-9 w-full rounded border border-ore-line bg-ore-surface pl-9 pr-3 text-sm text-ore-bright placeholder:text-ore-dim"
            @keyup.enter="performSearch"
          />
        </div>
        <nav class="flex flex-col gap-0.5">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="rounded-md px-3 py-2 text-sm font-medium transition-colors"
            :class="isActive(item.href)
              ? 'bg-ore-surface text-ore-copper'
              : 'text-ore-mid hover:bg-ore-surface hover:text-ore-bright'"
            @click="showMobileMenu = false"
          >
            {{ item.label }}
          </Link>
        </nav>
      </div>
    </div>
  </header>
</template>
