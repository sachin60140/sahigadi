<template>
    <Head title="Customer RC Searches" />

    <AdminLayout title="Customer RC Searches" eyebrow="RC operations">
        <RcSearchTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Customer lookup ledger</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Monitor paid RC searches and payment outcomes.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        See customer identity, registration requests, API status, collected amount and refund state without leaving the admin workspace.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a :href="actions.exportExcel" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">Export Excel</a>
                    <a :href="actions.exportPdf" class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Export PDF</a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <MetricTile label="Total searches" :value="stats.total" />
            <MetricTile label="Successful" :value="stats.successful" tone="teal" />
            <MetricTile label="Failed" :value="stats.failed" tone="red" />
            <MetricTile label="Refunded" :value="stats.refunded" tone="orange" />
            <MetricTile label="Net collected" :value="formatCurrency(stats.revenue)" tone="blue" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.3fr_170px_180px_180px_auto]" @submit.prevent="applyFilters">
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Customer or registration</span>
                    <input v-model="filterForm.search" class="admin-input" type="search" placeholder="Name, phone or BR01AB1234" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Status</span>
                    <select v-model="filterForm.status" class="admin-input">
                        <option value="">All statuses</option>
                        <option value="success">Successful</option>
                        <option value="failed">Failed</option>
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">From</span>
                    <input v-model="filterForm.from_date" class="admin-input" type="date" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">To</span>
                    <input v-model="filterForm.to_date" class="admin-input" type="date" />
                </label>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-1">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-teal-700">Filter</button>
                    <Link href="/admin/customer-vehicle-searches" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Clear</Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1060px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Registration</th>
                            <th class="px-5 py-3">Outcome</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="search in searches.data" :key="search.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ search.customer_name || 'Guest customer' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ search.customer_phone || 'Phone unavailable' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold uppercase text-slate-950">{{ search.registration_number }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-400">#{{ search.id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <StatusBadge :success="search.is_success" />
                                <p v-if="search.error_message" class="mt-2 max-w-[240px] truncate text-xs font-bold text-red-600" :title="search.error_message">{{ search.error_message }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ formatCurrency(search.paid_amount) }}</p>
                                <span v-if="search.is_refunded" class="mt-2 inline-flex rounded-md bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-100">Refunded</span>
                                <span v-else class="mt-2 inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Retained</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ search.created_at || 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="search.actions.show" class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-700">View</Link>
                                    <a v-if="search.is_success" :href="search.actions.pdf" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">PDF</a>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!searches.data.length">
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No customer RC searches found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try clearing or changing the current filters.</p>
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
    customer_name?: string | null;
    customer_phone?: string | null;
    registration_number: string;
    paid_amount: number;
    is_success: boolean;
    is_refunded: boolean;
    error_message?: string | null;
    created_at?: string | null;
    actions: { show: string; pdf: string };
};

const props = defineProps<{
    searches: { data: Search[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: { search: string; status: string; from_date: string; to_date: string };
    stats: { total: number; successful: number; failed: number; refunded: number; revenue: number };
    actions: { exportExcel: string; exportPdf: string };
}>();

const filterForm = reactive({ ...props.filters });
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params: Record<string, string> = {};
    Object.entries(filterForm).forEach(([key, value]) => {
        if (value) params[key] = value;
    });
    router.get('/admin/customer-vehicle-searches', params, { preserveState: true, preserveScroll: true });
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
            orange: 'border-orange-100 bg-orange-50 text-orange-700',
            blue: 'border-blue-100 bg-blue-50 text-blue-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-2xl font-semibold' }, typeof tileProps.value === 'number' ? new Intl.NumberFormat('en-IN').format(tileProps.value) : tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-semibold',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.success ? 'Successful' : 'Failed');
    },
});
</script>
