<template>
    <nav class="overflow-x-auto" aria-label="RC search sections">
        <div class="flex min-w-max gap-2">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                class="rounded-lg px-4 py-2.5 text-sm font-black transition"
                :class="isActive(item.match)
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
const currentPath = computed(() => String(page.url || '').split('?')[0]);

const items = [
    { label: 'Dealer searches', href: '/admin/vehicle-searches', match: '/admin/vehicle-searches' },
    { label: 'Customer searches', href: '/admin/customer-vehicle-searches', match: '/admin/customer-vehicle-searches' },
    { label: 'Combined ledger', href: '/admin/service-tracking/vehicle-search', match: '/admin/service-tracking/vehicle-search' },
    { label: 'Pricing', href: '/admin/vehicle-searches/settings', match: '/admin/vehicle-searches/settings' },
];

const isActive = (match: string) => {
    if (match === '/admin/vehicle-searches') {
        return currentPath.value === match || (
            currentPath.value.startsWith(`${match}/`)
            && currentPath.value !== '/admin/vehicle-searches/settings'
        );
    }

    return currentPath.value === match || currentPath.value.startsWith(`${match}/`);
};
</script>
