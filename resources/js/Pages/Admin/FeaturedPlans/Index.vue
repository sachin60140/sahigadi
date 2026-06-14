<template>
    <Head title="Featured Plans" />
    <AdminLayout title="Featured Plans" eyebrow="Revenue settings">
        <SettingsTabs />
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Listing promotion</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Price premium visibility by duration.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Manage the paid promotion options available for dealer and customer vehicle listings.</p>
                </div>
                <Link :href="actions.create" class="inline-flex w-fit rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600">Add featured plan</Link>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Total plans" :value="stats.total" />
                <MetricTile label="Active plans" :value="stats.active" tone="teal" />
                <MetricTile label="Inactive" :value="stats.inactive" tone="orange" />
                <MetricTile label="Live promotions" :value="stats.live_promotions" tone="blue" />
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Plan</th>
                            <th class="px-5 py-3">Duration</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Promotions</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="plan in plans" :key="plan.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-black text-slate-950">{{ plan.name }}</td>
                            <td class="px-5 py-4 font-black text-slate-700">{{ plan.duration_days }} days</td>
                            <td class="px-5 py-4 text-lg font-black text-teal-700">{{ formatCurrency(plan.price) }}</td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ plan.active_featured_listings_count }} active</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ plan.featured_listings_count }} total</p>
                            </td>
                            <td class="px-5 py-4"><StatusBadge :active="plan.is_active" /></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="plan.actions.edit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-white">Edit</Link>
                                    <button type="button" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-700 transition hover:bg-white" @click="deletePlan(plan)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!plans.length">
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No featured plans configured</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Create a promotion duration and price for vehicle sellers.</p>
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

type FeaturedPlan = {
    id: number;
    name: string;
    duration_days: number;
    price: number;
    is_active: boolean;
    featured_listings_count: number;
    active_featured_listings_count: number;
    actions: { edit: string; destroy: string };
};

defineProps<{ plans: FeaturedPlan[]; stats: { total: number; active: number; inactive: number; live_promotions: number }; actions: { create: string } }>();
const formatCurrency = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(value)}`;
const deletePlan = (plan: FeaturedPlan) => {
    const impact = plan.featured_listings_count ? ` This also deletes ${plan.featured_listings_count} linked promotion records.` : '';
    if (window.confirm(`Delete ${plan.name}?${impact}`)) router.delete(plan.actions.destroy, { preserveScroll: true });
};

const MetricTile = defineComponent({
    props: { label: { type: String, required: true }, value: { type: Number, required: true }, tone: { type: String, default: 'slate' } },
    setup(tileProps) {
        const classes = () => tileProps.tone === 'teal'
            ? 'border-teal-100 bg-teal-50 text-teal-700'
            : tileProps.tone === 'orange'
                ? 'border-orange-100 bg-orange-50 text-orange-700'
                : tileProps.tone === 'blue'
                    ? 'border-blue-100 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-slate-50 text-slate-900';
        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-black' }, new Intl.NumberFormat('en-IN').format(tileProps.value)),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { active: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', { class: ['inline-flex rounded-md px-2.5 py-1 text-xs font-black', badgeProps.active ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'] }, badgeProps.active ? 'Active' : 'Inactive');
    },
});
</script>
