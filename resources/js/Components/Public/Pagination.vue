<template>
    <div v-if="links.length > 3" class="flex flex-wrap -mb-1 mt-8 justify-center gap-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                 class="px-4 py-2 text-sm leading-4 text-gray-400 border rounded-md" 
                 v-html="link.label">
            </div>
            <Link v-else 
                  class="px-4 py-2 text-sm leading-4 border rounded-md hover:bg-[#071226] hover:text-white focus:border-[#071226] focus:text-[#071226] transition-colors"
                  :class="{ 'bg-[#071226] text-white': link.active }"
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
