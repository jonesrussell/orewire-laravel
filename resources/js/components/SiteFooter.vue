<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <footer class="border-t border-ore-line bg-ore-deep">
    <div class="mx-auto max-w-[1400px] px-4 py-12 sm:px-6 lg:px-8">
      <div class="grid gap-8 lg:grid-cols-3 lg:gap-12">
        <div class="lg:col-span-2">
          <span class="font-serif text-lg font-bold tracking-[0.04em] text-ore-bright">
            OREWIRE
          </span>
          <p class="mt-1 text-xs font-medium uppercase tracking-[0.15em] text-ore-copper">
            Canadian Mining. Global Reach. Clear Reporting.
          </p>
          <p class="mt-4 max-w-lg text-sm leading-relaxed text-ore-mid">
            Orewire delivers clear, factual reporting on the companies, projects, and policies
            shaping Canada's mining sector &mdash; at home and around the world. We serve professionals,
            communities, and decision-makers who depend on accurate, timely information about
            exploration, development, operations, and the resource economy.
          </p>
        </div>

        <nav class="flex flex-col gap-2">
          <span class="mb-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-ore-mid">
            Navigate
          </span>
          <Link href="/" class="text-sm text-ore-mid transition-colors hover:text-ore-copper">
            Home
          </Link>
          <Link href="/articles" class="text-sm text-ore-mid transition-colors hover:text-ore-copper">
            Articles
          </Link>
          <Link href="/drill-results" class="text-sm text-ore-mid transition-colors hover:text-ore-copper">
            Drill Results
          </Link>
        </nav>
      </div>

      <div class="mt-10 border-t border-ore-line-faint pt-6">
        <p class="text-xs text-ore-dim">
          &copy; {{ new Date().getFullYear() }} OreWire. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>
