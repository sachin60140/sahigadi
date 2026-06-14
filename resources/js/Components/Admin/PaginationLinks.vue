<template>
    <div v-if="links.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-lg border px-3 py-2 text-sm font-bold leading-4 transition sm:px-4"
                :class="link.active
                    ? 'border-teal-700 bg-teal-700 text-white'
                    : 'border-slate-200 bg-white text-slate-700 hover:bg-teal-700 hover:text-white'"
                v-html="link.label"
            />
            <span
                v-else
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold leading-4 text-slate-400 sm:px-4"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: Array<{ url: string | null; label: string; active: boolean }>;
}>();
</script>
