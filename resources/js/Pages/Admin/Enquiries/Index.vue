<template>
    <Head title="Vehicle Enquiries" />

    <AdminLayout title="Vehicle Enquiries" eyebrow="Buyer leads">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Lead desk</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Turn buyer interest into timely conversations.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Review vehicle enquiries, identify the seller and continue each conversation by phone or WhatsApp.
                    </p>
                </div>
                <a :href="exportUrl" class="inline-flex w-fit rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
                    Export CSV
                </a>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Total enquiries" :value="stats.total" />
                <MetricTile label="New leads" :value="stats.new" tone="orange" />
                <MetricTile label="Contacted" :value="stats.contacted" tone="teal" />
                <MetricTile label="Dealer leads" :value="stats.dealer" tone="blue" />
            </div>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.2fr_160px_190px_170px_170px_auto]" @submit.prevent="applyFilters">
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Customer</span>
                    <input v-model="filterForm.search" class="admin-input" type="search" placeholder="Name or phone" />
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Status</span>
                    <select v-model="filterForm.status" class="admin-input">
                        <option value="">All statuses</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Dealer</span>
                    <select v-model="filterForm.dealer_id" class="admin-input">
                        <option value="">All dealers</option>
                        <option v-for="dealer in dealers" :key="dealer.id" :value="String(dealer.id)">{{ dealer.name }}</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">From</span>
                    <input v-model="filterForm.date_from" class="admin-input" type="date" />
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">To</span>
                    <input v-model="filterForm.date_to" class="admin-input" type="date" />
                </label>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-1">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-teal-700">
                        Filter
                    </button>
                    <Link href="/admin/enquiries" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Clear
                    </Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1180px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Received</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Vehicle</th>
                            <th class="px-5 py-3">Registration</th>
                            <th class="px-5 py-3">Listed by</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="enquiry in enquiries.data" :key="enquiry.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-700">{{ enquiry.created_at || 'N/A' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-400">#{{ enquiry.id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ enquiry.customer_name }}</p>
                                <a :href="`tel:${enquiry.customer_phone}`" class="mt-1 block text-xs font-bold text-teal-700">{{ enquiry.customer_phone }}</a>
                                <p v-if="enquiry.customer_email" class="mt-1 max-w-[220px] truncate text-xs font-semibold text-slate-500">{{ enquiry.customer_email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <template v-if="enquiry.vehicle">
                                    <Link :href="enquiry.vehicle.admin_url" class="block max-w-[260px] truncate font-semibold text-slate-950 hover:text-teal-700">
                                        {{ enquiry.vehicle.title }}
                                    </Link>
                                    <p class="mt-1 text-xs font-bold text-slate-500">{{ formatCurrency(enquiry.vehicle.price) }}</p>
                                </template>
                                <span v-else class="inline-flex rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100">Vehicle deleted</span>
                            </td>
                            <td class="px-5 py-4 font-semibold uppercase text-slate-700">{{ enquiry.vehicle?.registration_number || '-' }}</td>
                            <td class="px-5 py-4">
                                <template v-if="enquiry.listed_by === 'dealer'">
                                    <Link v-if="enquiry.dealer" :href="enquiry.dealer.show_url" class="font-semibold text-slate-950 hover:text-teal-700">
                                        {{ enquiry.dealer.company_name || enquiry.dealer.name }}
                                    </Link>
                                    <p v-else class="font-semibold text-red-700">Dealer deleted</p>
                                    <span class="mt-2 inline-flex rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">Dealer</span>
                                </template>
                                <template v-else>
                                    <p class="font-semibold text-slate-950">{{ enquiry.vehicle?.owner_name || 'Customer' }}</p>
                                    <span class="mt-2 inline-flex rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">Customer</span>
                                </template>
                            </td>
                            <td class="px-5 py-4"><StatusBadge :status="enquiry.status" /></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="enquiry.actions.show" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        View
                                    </Link>
                                    <button
                                        v-if="enquiry.status === 'new'"
                                        type="button"
                                        class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-800"
                                        @click="markContacted(enquiry)"
                                    >
                                        Contacted
                                    </button>
                                    <a
                                        v-if="enquiry.whatsapp_url"
                                        :href="enquiry.whatsapp_url"
                                        target="_blank"
                                        rel="noreferrer"
                                        class="rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-700 transition hover:bg-white"
                                    >
                                        WhatsApp
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!enquiries.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No vehicle enquiries found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try clearing or changing the current filters.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="enquiries.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Vehicle = {
    title: string;
    price: number;
    registration_number?: string | null;
    owner_name?: string | null;
    admin_url: string;
};

type Enquiry = {
    id: number;
    customer_name: string;
    customer_email?: string | null;
    customer_phone: string;
    status: string;
    created_at?: string | null;
    vehicle?: Vehicle | null;
    dealer?: { name: string; company_name?: string | null; show_url: string } | null;
    listed_by: string;
    whatsapp_url?: string | null;
    actions: { show: string; contacted: string };
};

const props = defineProps<{
    enquiries: { data: Enquiry[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    dealers: Array<{ id: number; name: string }>;
    filters: { search: string; status: string; dealer_id: string; date_from: string; date_to: string; car_id: string };
    stats: { total: number; new: number; contacted: number; dealer: number };
    exportUrl: string;
}>();

const filterForm = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    dealer_id: props.filters.dealer_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params: Record<string, string> = {};
    Object.entries(filterForm).forEach(([key, value]) => {
        if (value) params[key] = value;
    });
    router.get('/admin/enquiries', params, { preserveState: true, preserveScroll: true });
};

const markContacted = (enquiry: Enquiry) => {
    router.post(enquiry.actions.contacted, {}, { preserveScroll: true });
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
            if (tileProps.tone === 'blue') return 'border-blue-100 bg-blue-50 text-blue-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };
        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-semibold' }, new Intl.NumberFormat('en-IN').format(tileProps.value)),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-semibold capitalize',
                badgeProps.status === 'new'
                    ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                    : 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
            ],
        }, badgeProps.status);
    },
});
</script>
