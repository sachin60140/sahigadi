<template>
    <div v-if="links.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null"
                 class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold leading-4 text-slate-400 sm:px-4"
                 v-html="link.label">
            </div>
            <Link v-else
                  class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold leading-4 transition hover:bg-teal-700 hover:text-white focus:border-teal-700 focus:text-teal-700 sm:px-4"
                  :class="{ 'bg-teal-700 text-white border-teal-700': link.active }"
                  :href="link.url"
                  :only="['cars']"
                  v-html="link.label">
            </Link>
        </template>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: any[];
}>();
</script>
