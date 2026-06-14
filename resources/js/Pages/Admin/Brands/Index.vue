<template>
    <Head title="Car Brands" />

    <AdminLayout title="Car Brands" eyebrow="Catalog settings">
        <SettingsTabs />

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Vehicle taxonomy</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Keep inventory brands clean and consistent.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Manage the brand names and logos used across dealer cars, customer listings and public filters.
                    </p>
                </div>
                <Link :href="actions.create" class="inline-flex w-fit rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600">
                    Add brand
                </Link>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Total brands" :value="stats.total" />
                <MetricTile label="Active" :value="stats.active" tone="teal" />
                <MetricTile label="Inactive" :value="stats.inactive" tone="orange" />
                <MetricTile label="Vehicle links" :value="stats.vehicle_links" tone="blue" />
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Brand</th>
                            <th class="px-5 py-3">Slug</th>
                            <th class="px-5 py-3">Inventory usage</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="brand in brands" :key="brand.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-12 w-16 shrink-0 place-items-center overflow-hidden rounded-lg border border-slate-200 bg-white p-2">
                                        <img v-if="brand.logo_url" :src="brand.logo_url" :alt="brand.name" class="max-h-full max-w-full object-contain" />
                                        <span v-else class="text-lg font-black text-slate-300">{{ brand.name.charAt(0) }}</span>
                                    </span>
                                    <p class="font-black text-slate-950">{{ brand.name }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4"><code class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ brand.slug }}</code></td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ brand.inventory_count }} vehicles</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ brand.cars_count }} dealer / {{ brand.customer_listings_count }} customer</p>
                            </td>
                            <td class="px-5 py-4"><StatusBadge :active="brand.is_active" /></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="brand.actions.edit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-white">Edit</Link>
                                    <button type="button" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-700 transition hover:bg-white" @click="deleteBrand(brand)">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!brands.length">
                            <td colspan="5" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No brands configured</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Add the first vehicle brand to start organizing inventory.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';

type Brand = {
    id: number;
    name: string;
    slug: string;
    logo_url?: string | null;
    is_active: boolean;
    cars_count: number;
    customer_listings_count: number;
    inventory_count: number;
    actions: { edit: string; destroy: string };
};

defineProps<{
    brands: Brand[];
    stats: { total: number; active: number; inactive: number; vehicle_links: number };
    actions: { create: string };
}>();

const deleteBrand = (brand: Brand) => {
    const usage = brand.inventory_count
        ? ` ${brand.inventory_count} vehicle records will keep working but their brand will be cleared.`
        : '';
    if (window.confirm(`Delete ${brand.name}?${usage}`)) {
        router.delete(brand.actions.destroy, { preserveScroll: true });
    }
};

const MetricTile = defineComponent({
    props: { label: { type: String, required: true }, value: { type: Number, required: true }, tone: { type: String, default: 'slate' } },
    setup(tileProps) {
        const classes = () => {
            if (tileProps.tone === 'teal') return 'border-teal-100 bg-teal-50 text-teal-700';
            if (tileProps.tone === 'orange') return 'border-orange-100 bg-orange-50 text-orange-700';
            if (tileProps.tone === 'blue') return 'border-blue-100 bg-blue-50 text-blue-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };
        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-black' }, new Intl.NumberFormat('en-IN').format(tileProps.value)),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { active: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black',
                badgeProps.active
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
            ],
        }, badgeProps.active ? 'Active' : 'Inactive');
    },
});
</script>
