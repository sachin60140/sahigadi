<template>
    <Head title="Combined RC Search Ledger" />

    <AdminLayout title="Combined RC Search Ledger" eyebrow="Service tracking">
        <RcSearchTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-teal-700">Unified activity</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Dealer and customer RC usage in one timeline.</h2>
            <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                Compare channel volume, lookup success and service revenue while retaining direct access to each source record.
            </p>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <MetricTile label="Filtered searches" :value="stats.total" />
            <MetricTile label="Successful" :value="stats.successful" tone="teal" />
            <MetricTile label="Failed" :value="stats.failed" tone="red" />
            <MetricTile label="Revenue" :value="formatCurrency(stats.revenue)" tone="blue" />
            <MetricTile label="Customer charge" :value="formatCurrency(stats.customer_charge)" tone="orange" />
            <MetricTile label="Dealer charge" :value="formatCurrency(stats.dealer_charge)" tone="slate" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.3fr_190px_190px_auto]" @submit.prevent="applyFilters">
                <label>
                    <span class="mb-2 block text-sm font-black text-slate-700">Registration number</span>
                    <input v-model="filterForm.search" class="admin-input uppercase" type="search" placeholder="BR01AB1234" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-black text-slate-700">From</span>
                    <input v-model="filterForm.from_date" class="admin-input" type="date" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-black text-slate-700">To</span>
                    <input v-model="filterForm.to_date" class="admin-input" type="date" />
                </label>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-1">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-teal-700">Filter</button>
                    <Link href="/admin/service-tracking/vehicle-search" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">Clear</Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Channel</th>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Registration</th>
                            <th class="px-5 py-3">Outcome</th>
                            <th class="px-5 py-3">Charge</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="search in searches.data" :key="`${search.type}-${search.id}`" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-md px-2.5 py-1 text-xs font-black"
                                    :class="search.type === 'dealer'
                                        ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100'
                                        : 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'"
                                >
                                    {{ search.type === 'dealer' ? 'Dealer' : 'Customer' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ search.user_name }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ search.phone || 'Phone unavailable' }}</p>
                            </td>
                            <td class="px-5 py-4 font-black uppercase text-slate-950">{{ search.vehicle_number || 'N/A' }}</td>
                            <td class="px-5 py-4"><StatusBadge :success="search.is_success" /></td>
                            <td class="px-5 py-4 font-black text-slate-950">{{ formatCurrency(search.charge_amount) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ search.created_at || 'N/A' }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link :href="search.show_url" class="inline-flex rounded-lg bg-slate-950 px-3 py-2 text-xs font-black text-white transition hover:bg-teal-700">View record</Link>
                            </td>
                        </tr>
                        <tr v-if="!searches.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No RC activity found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try clearing or changing the date and registration filters.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="searches.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';
import RcSearchTabs from '@/Components/Admin/RcSearchTabs.vue';

type Search = {
    id: number;
    type: 'dealer' | 'customer';
    user_name: string;
    phone?: string | null;
    vehicle_number?: string | null;
    is_success: boolean;
    charge_amount: number;
    created_at?: string | null;
    show_url: string;
};

const props = defineProps<{
    searches: { data: Search[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: { search: string; from_date: string; to_date: string };
    stats: { total: number; successful: number; failed: number; revenue: number; customer_charge: number; dealer_charge: number };
}>();

const filterForm = reactive({ ...props.filters });
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params: Record<string, string> = {};
    Object.entries(filterForm).forEach(([key, value]) => {
        if (value) params[key] = value;
    });
    router.get('/admin/service-tracking/vehicle-search', params, { preserveState: true, preserveScroll: true });
};

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: [String, Number], required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const colors: Record<string, string> = {
            slate: 'border-slate-200 bg-white text-slate-950',
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            red: 'border-red-100 bg-red-50 text-red-700',
            blue: 'border-blue-100 bg-blue-50 text-blue-700',
            orange: 'border-orange-100 bg-orange-50 text-orange-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-2xl font-black' }, typeof tileProps.value === 'number' ? new Intl.NumberFormat('en-IN').format(tileProps.value) : tileProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.success ? 'Successful' : 'Failed');
    },
});
</script>
