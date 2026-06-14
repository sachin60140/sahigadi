<template>
    <nav class="overflow-x-auto" aria-label="Challan PDF sections">
        <div class="flex min-w-max gap-2">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                class="rounded-lg px-4 py-2.5 text-sm font-black transition"
                :class="isActive(item)
                    ? 'bg-slate-950 text-white shadow-sm'
                    : 'border border-slate-200 bg-white text-slate-600 hover:border-teal-200 hover:text-teal-700'"
            >
                {{ item.label }}
            </Link>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const path = computed(() => String(page.url || '').split('?')[0]);
const items = [
    { label: 'Service settings', href: '/admin/challan-pdf' },
    { label: 'Search logs', href: '/admin/challan-pdf/logs' },
];

const isActive = (item: { href: string }) => path.value === item.href;
</script>
