<template>
    <nav class="overflow-x-auto" aria-label="E-Challan sections">
        <div class="flex min-w-max gap-2">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                class="rounded-lg px-4 py-2.5 text-sm font-semibold transition"
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
    { label: 'Dealer searches', href: '/admin/challan-searches', match: '/admin/challan-searches', settings: '/admin/challan-searches/settings' },
    { label: 'Customer payments', href: '/admin/customer-transactions?type=challan', match: '/admin/customer-transactions' },
    { label: 'Combined ledger', href: '/admin/service-tracking/challan-search', match: '/admin/service-tracking/challan-search' },
    { label: 'Pricing', href: '/admin/challan-searches/settings', match: '/admin/challan-searches/settings' },
];

const isActive = (item: { match: string; settings?: string }) => {
    if (item.match === '/admin/challan-searches') {
        return (path.value === item.match || path.value.startsWith(`${item.match}/`)) && path.value !== item.settings;
    }
    return path.value === item.match || path.value.startsWith(`${item.match}/`);
};
</script>
