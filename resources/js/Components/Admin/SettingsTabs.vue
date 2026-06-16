<template>
    <nav class="mb-5 overflow-x-auto border-b border-slate-200" aria-label="Catalog settings">
        <div class="flex min-w-max gap-1">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                :class="[
                    'border-b-2 px-4 py-3 text-sm font-semibold transition',
                    isActive(item)
                        ? 'border-teal-700 text-teal-700'
                        : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-950',
                ]"
            >
                {{ item.label }}
            </Link>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

type TabItem = { label: string; href: string; match: string };

const page = usePage();
const currentPath = computed(() => String(page.url || '').split('?')[0]);
const items: TabItem[] = [
    { label: 'Brands', href: '/admin/brands', match: '/admin/brands' },
    { label: 'Subscription Plans', href: '/admin/plans', match: '/admin/plans' },
    { label: 'Featured Plans', href: '/admin/featured-plans', match: '/admin/featured-plans' },
];

const isActive = (item: TabItem) => currentPath.value === item.match || currentPath.value.startsWith(`${item.match}/`);
</script>
