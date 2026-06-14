<template>
    <Head title="Manage Dealers" />

    <AdminLayout title="Manage Dealers" eyebrow="Dealer operations">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Dealer network</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Review and manage registered dealers.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Track approvals, wallet balance, listing volume and GST readiness from one responsive admin view.
                    </p>
                </div>
                <Link href="/admin/dealers/create" class="inline-flex w-fit rounded-lg bg-orange-500 px-5 py-3 text-sm font-black text-white transition hover:bg-orange-600">
                    Add Dealer
                </Link>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Total dealers" :value="stats.total" />
                <MetricTile label="Approved" :value="stats.approved" tone="teal" />
                <MetricTile label="Pending" :value="stats.pending" tone="orange" />
                <MetricTile label="Rejected" :value="stats.rejected" tone="red" />
            </div>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-[220px_1fr_auto]" @submit.prevent="applyFilters">
                <label class="block">
                    <span class="mb-2 block text-sm font-black text-slate-700">Status</span>
                    <select v-model="filterForm.status" class="admin-input">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-black text-slate-700">Search</span>
                    <input v-model="filterForm.search" class="admin-input" type="search" placeholder="ID, name, email or phone" />
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-teal-700">Filter</button>
                    <Link href="/admin/dealers" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                        Clear
                    </Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1080px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Dealer</th>
                            <th class="px-5 py-3">Contact</th>
                            <th class="px-5 py-3">Location</th>
                            <th class="px-5 py-3">Wallet</th>
                            <th class="px-5 py-3">Listings</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Joined</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="dealer in dealers.data" :key="dealer.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ dealer.name }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ dealer.dealer_unique_id }}</p>
                                <p v-if="dealer.company_name" class="mt-1 max-w-[240px] truncate text-xs font-semibold text-slate-500">{{ dealer.company_name }}</p>
                                <span v-if="dealer.gst_verified" class="mt-2 inline-flex rounded-md bg-teal-50 px-2.5 py-1 text-xs font-black text-teal-700 ring-1 ring-teal-100">
                                    GST verified
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-[240px] truncate font-semibold text-slate-700">{{ dealer.email }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ dealer.phone || 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-700">{{ dealer.city || 'N/A' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ dealer.state || 'N/A' }}</p>
                            </td>
                            <td class="px-5 py-4 font-black text-teal-700">{{ formatCurrency(dealer.wallet_balance) }}</td>
                            <td class="px-5 py-4 font-black text-slate-950">{{ dealer.cars_count }}</td>
                            <td class="px-5 py-4">
                                <StatusBadge :status="dealer.status" />
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ dealer.joined_at || 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="dealer.show_url" class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-black text-teal-700 transition hover:bg-white">
                                        View
                                    </Link>
                                    <button
                                        type="button"
                                        class="rounded-lg border px-4 py-2 text-xs font-black transition"
                                        :class="dealer.status === 'approved' ? 'border-red-200 bg-red-50 text-red-700 hover:bg-white' : 'border-teal-200 bg-teal-50 text-teal-700 hover:bg-white'"
                                        @click="toggleDealer(dealer)"
                                    >
                                        {{ dealer.status === 'approved' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!dealers.data.length">
                            <td colspan="8" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No dealers found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try another search term or status filter.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="dealers.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Dealer = {
    id: number;
    dealer_unique_id?: string | null;
    name: string;
    email: string;
    phone?: string | null;
    company_name?: string | null;
    city?: string | null;
    state?: string | null;
    status: string;
    wallet_balance: number;
    cars_count: number;
    gst_verified: boolean;
    joined_at?: string | null;
    show_url: string;
    toggle_status_url: string;
};

const props = defineProps<{
    dealers: { data: Dealer[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: { status: string; search: string };
    stats: { total: number; approved: number; pending: number; rejected: number };
}>();

const filterForm = reactive({
    status: props.filters.status || 'all',
    search: props.filters.search || '',
});

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params: Record<string, string> = {};
    if (filterForm.status && filterForm.status !== 'all') {
        params.status = filterForm.status;
    }
    if (filterForm.search) {
        params.search = filterForm.search;
    }
    router.get('/admin/dealers', params, { preserveScroll: true, preserveState: true });
};

const toggleDealer = (dealer: Dealer) => {
    const message = dealer.status === 'approved' ? 'Deactivate this dealer?' : 'Activate this dealer?';
    if (!window.confirm(message)) {
        return;
    }

    router.post(dealer.toggle_status_url, {}, { preserveScroll: true });
};

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const classes = () => {
            if (tileProps.tone === 'teal') return 'border-teal-100 bg-teal-50 text-teal-700';
            if (tileProps.tone === 'orange') return 'border-orange-100 bg-orange-50 text-orange-700';
            if (tileProps.tone === 'red') return 'border-red-100 bg-red-50 text-red-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };

        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-black' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize',
                badgeProps.status === 'approved'
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : badgeProps.status === 'pending'
                        ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                        : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.status);
    },
});
</script>

<style scoped>
.admin-input {
    min-height: 48px;
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 12px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(30 41 59);
    outline: none;
}
.admin-input:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
