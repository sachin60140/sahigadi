<template>
    <nav class="overflow-x-auto" aria-label="Service history sections">
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
    { label: 'General', href: '/admin/service-histories', match: '/admin/service-histories', settings: '/admin/service-histories/settings' },
    { label: 'Maruti dealers', href: '/admin/maruti-service-histories', match: '/admin/maruti-service-histories' },
    { label: 'Maruti customers', href: '/admin/customer-maruti-service-histories', match: '/admin/customer-maruti-service-histories' },
    { label: 'Mahindra customers', href: '/admin/mahindra-service-histories', match: '/admin/mahindra-service-histories' },
    { label: 'Combined ledger', href: '/admin/service-tracking/service-history', match: '/admin/service-tracking/service-history' },
    { label: 'Pricing', href: '/admin/service-histories/settings', match: '/admin/service-histories/settings' },
];

const isActive = (item: { match: string; settings?: string }) => {
    if (item.match === '/admin/service-histories') {
        return (path.value === item.match || path.value.startsWith(`${item.match}/`)) && path.value !== item.settings;
    }
    return path.value === item.match || path.value.startsWith(`${item.match}/`);
};
</script>
