<template>
    <Head title="API Usage" />

    <AdminLayout title="Dealer API Usage" eyebrow="Partner integrations">
        <RcSearchTabs />

        <div v-if="!stats.api_enabled" class="mt-5 rounded-lg border border-orange-100 bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-700">
            The dealer API master switch is off. No API calls will be accepted until it is enabled in
            <Link :href="actions.settings" class="underline">RC Search Pricing</Link>.
        </div>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <MetricTile label="Total API calls" :value="stats.total" />
            <MetricTile label="Successful" :value="stats.successful" tone="teal" />
            <MetricTile label="Failed" :value="stats.failed" tone="red" />
            <MetricTile label="API revenue" :value="formatCurrency(stats.revenue)" tone="blue" />
            <MetricTile label="Enabled dealers" :value="stats.enabled_dealers" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_200px_180px_auto] md:items-end">
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Vehicle search</span>
                    <input v-model="filterForm.search" class="admin-input uppercase" type="search" placeholder="BR01AB1234" @keyup.enter="applyFilters" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Dealer</span>
                    <select v-model="filterForm.dealer_id" class="admin-input">
                        <option value="">All dealers</option>
                        <option v-for="dealer in dealers" :key="dealer.id" :value="String(dealer.id)">{{ dealer.name }}</option>
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Status</span>
                    <select v-model="filterForm.status" class="admin-input">
                        <option value="">All statuses</option>
                        <option value="success">Successful</option>
                        <option value="failed">Failed</option>
                    </select>
                </label>
                <div class="flex gap-2">
                    <button type="button" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800" @click="applyFilters">Filter</button>
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="clearFilters">Clear</button>
                </div>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Dealer</th>
                            <th class="px-5 py-3">Vehicle</th>
                            <th class="px-5 py-3">Result</th>
                            <th class="px-5 py-3 text-right">Charge</th>
                            <th class="px-5 py-3">IP address</th>
                            <th class="px-5 py-3">Called at</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="call in calls.data" :key="call.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ call.dealer }}</td>
                            <td class="px-5 py-4 font-semibold uppercase text-slate-700">{{ call.registration_number }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-md px-2.5 py-1 text-xs font-semibold" :class="call.is_success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'">
                                    {{ call.is_success ? 'Success' : 'Failed' }}
                                </span>
                                <p v-if="!call.is_success && call.error_message" class="mt-1 max-w-[260px] truncate text-xs font-medium text-slate-500">{{ call.error_message }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ formatCurrency(call.charge) }}</td>
                            <td class="px-5 py-4 font-medium text-slate-500">{{ call.ip_address || '-' }}</td>
                            <td class="px-5 py-4 font-medium text-slate-500">{{ call.created_at }}</td>
                        </tr>
                        <tr v-if="!calls.data.length">
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-base font-bold text-slate-950">No API calls yet</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">Usage appears here once a dealer starts calling the vehicle search API.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="calls.data.length" class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="calls.links" />
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

const props = defineProps<{
    calls: { data: Array<any>; links: Array<any> };
    dealers: Array<{ id: number; name: string }>;
    filters: { search: string; dealer_id: string; status: string };
    stats: { total: number; successful: number; failed: number; revenue: number; enabled_dealers: number; api_enabled: boolean; charge: number };
    actions: { settings: string };
}>();

const filterForm = reactive({
    search: props.filters.search || '',
    dealer_id: props.filters.dealer_id || '',
    status: props.filters.status || '',
});

const applyFilters = () => {
    const params: Record<string, string> = {};
    if (filterForm.search) params.search = filterForm.search;
    if (filterForm.dealer_id) params.dealer_id = filterForm.dealer_id;
    if (filterForm.status) params.status = filterForm.status;
    router.get('/admin/vehicle-searches/api-usage', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    filterForm.search = '';
    filterForm.dealer_id = '';
    filterForm.status = '';
    router.get('/admin/vehicle-searches/api-usage', {}, { preserveState: true, preserveScroll: true });
};

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

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
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-2xl font-bold tracking-tight' }, typeof tileProps.value === 'number' ? new Intl.NumberFormat('en-IN').format(tileProps.value) : tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});
</script>
