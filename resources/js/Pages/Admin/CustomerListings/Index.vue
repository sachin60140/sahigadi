<template>
    <Head title="Customer Car Listings" />

    <AdminLayout title="Customer Car Listings" eyebrow="Customer inventory">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Individual sellers</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Manage customer-submitted car listings.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Review pending listings, contact owners, assign featured placement and export inventory reports.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link href="/admin/customer-listings/create" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Create Listing
                    </Link>
                    <a :href="exportUrls.excel" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">Export Excel</a>
                    <a :href="exportUrls.pdf" class="rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Export PDF</a>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <MetricTile label="Total" :value="stats.total" />
                <MetricTile label="Pending" :value="stats.pending" tone="orange" />
                <MetricTile label="Approved" :value="stats.approved" tone="teal" />
                <MetricTile label="Rejected" :value="stats.rejected" tone="red" />
                <MetricTile label="Featured" :value="stats.featured" tone="amber" />
            </div>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-[170px_170px_180px_1fr_auto]" @submit.prevent="applyFilters">
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Status</span>
                    <select v-model="filterForm.status" class="admin-input">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Date</span>
                    <input v-model="filterForm.date" class="admin-input" type="date" />
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">City</span>
                    <select v-model="filterForm.city" class="admin-input">
                        <option value="">All Cities</option>
                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Search</span>
                    <input v-model="filterForm.search" class="admin-input" type="search" placeholder="Title, model, phone or WhatsApp" />
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-teal-700">Filter</button>
                    <Link href="/admin/customer-listings" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Clear
                    </Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1180px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Listing</th>
                            <th class="px-5 py-3">Owner</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Featured</th>
                            <th class="px-5 py-3">Location</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="listing in listings.data" :key="listing.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img v-if="listing.image_url" :src="listing.image_url" :alt="listing.title" class="h-16 w-24 rounded-lg object-cover" />
                                    <div v-else class="grid h-16 w-24 place-items-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-400">No image</div>
                                    <div class="min-w-0">
                                        <p class="max-w-[260px] truncate font-semibold text-slate-950">{{ listing.title }}</p>
                                        <p class="mt-1 text-xs font-bold text-slate-500">#{{ listing.unique_id }} / {{ listing.brand || 'No brand' }} / {{ listing.year || 'N/A' }}</p>
                                        <span v-if="listing.registration_number" class="mt-2 inline-flex rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            {{ listing.registration_number }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ listing.owner_name || 'N/A' }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ listing.owner_phone || 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ formatCurrency(listing.price) }}</td>
                            <td class="px-5 py-4">
                                <FeaturedState :listing="listing" @feature="openFeatureModal" @remove="removeFeatured" />
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-700">{{ listing.city || 'N/A' }}</p>
                                <a v-if="listing.map_url" :href="listing.map_url" target="_blank" rel="noreferrer" class="mt-2 inline-flex rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">Map</a>
                            </td>
                            <td class="px-5 py-4">
                                <StatusBadge :status="listing.status" />
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="listing.actions.show" class="rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-700 transition hover:bg-white">View</Link>
                                    <Link :href="listing.actions.edit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit</Link>
                                    <button v-if="listing.status === 'pending'" type="button" class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-semibold text-white" @click="approveListing(listing)">Approve</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!listings.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No customer listings found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try another status, date, city or search filter.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="listings.links" />
            </div>
        </section>

        <FeatureModal v-if="featureListing" :listing="featureListing" :plans="featuredPlans" @close="featureListing = null" @submit="featureListingWithPlan" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type FeaturedPlan = { id: number; name: string; duration_days: number; price: number };
type Listing = {
    id: number;
    title: string;
    unique_id: string;
    brand?: string | null;
    year?: number | null;
    price: number;
    city?: string | null;
    owner_name?: string | null;
    owner_phone?: string | null;
    registration_number?: string | null;
    status: string;
    is_featured: boolean;
    featured_expires_at?: string | null;
    paid_featured_active: boolean;
    image_url?: string | null;
    map_url?: string | null;
    actions: Record<string, string>;
};

const props = defineProps<{
    listings: { data: Listing[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    pendingCount: number;
    featuredPlans: FeaturedPlan[];
    filters: { status: string; date: string; city: string; search: string };
    cities: string[];
    stats: { total: number; pending: number; approved: number; rejected: number; featured: number };
    exportUrls: { excel: string; pdf: string };
}>();

const featureListing = ref<Listing | null>(null);
const filterForm = reactive({
    status: props.filters.status || 'all',
    date: props.filters.date || '',
    city: props.filters.city || '',
    search: props.filters.search || '',
});

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params: Record<string, string> = {};
    if (filterForm.status && filterForm.status !== 'all') params.status = filterForm.status;
    if (filterForm.date) params.date = filterForm.date;
    if (filterForm.city) params.city = filterForm.city;
    if (filterForm.search) params.search = filterForm.search;
    router.get('/admin/customer-listings', params, { preserveScroll: true, preserveState: true });
};

const approveListing = (listing: Listing) => router.post(listing.actions.approve, {}, { preserveScroll: true });
const openFeatureModal = (listing: Listing) => { featureListing.value = listing; };
const removeFeatured = (listing: Listing) => {
    if (window.confirm('Remove featured status from this listing?')) {
        router.post(listing.actions.remove_featured, {}, { preserveScroll: true });
    }
};
const featureListingWithPlan = (days: number) => {
    if (!featureListing.value) return;
    router.post(featureListing.value.actions.featured, { days }, {
        preserveScroll: true,
        onSuccess: () => { featureListing.value = null; },
    });
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
            if (tileProps.tone === 'amber') return 'border-amber-100 bg-amber-50 text-amber-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };
        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-semibold' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex w-fit rounded-md px-2.5 py-1 text-xs font-semibold capitalize',
                badgeProps.status === 'approved'
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : badgeProps.status === 'pending'
                        ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                        : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.status);
    },
});

const FeaturedState = defineComponent({
    props: { listing: { type: Object as () => Listing, required: true } },
    emits: ['feature', 'remove'],
    setup(stateProps, { emit }) {
        return () => stateProps.listing.is_featured
            ? h('div', [
                h('span', { class: 'inline-flex rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100' }, 'Featured'),
                stateProps.listing.featured_expires_at ? h('p', { class: 'mt-1 text-xs font-semibold text-slate-500' }, `Till ${stateProps.listing.featured_expires_at}`) : null,
                stateProps.listing.paid_featured_active
                    ? h('span', { class: 'mt-2 inline-flex rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100' }, 'User paid')
                    : h('button', { type: 'button', class: 'mt-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700', onClick: () => emit('remove', stateProps.listing) }, 'Remove'),
            ])
            : h('button', { type: 'button', class: 'rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700', onClick: () => emit('feature', stateProps.listing) }, 'Feature');
    },
});

const FeatureModal = defineComponent({
    props: {
        listing: { type: Object as () => Listing, required: true },
        plans: { type: Array as () => FeaturedPlan[], required: true },
    },
    emits: ['close', 'submit'],
    setup(modalProps, { emit }) {
        const allowed = modalProps.plans.filter((plan) => [7, 14, 30].includes(plan.duration_days));
        const selectedDays = ref(allowed[0]?.duration_days || 7);
        return () => h('div', { class: 'fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4' }, [
            h('button', { class: 'absolute inset-0', type: 'button', 'aria-label': 'Close modal', onClick: () => emit('close') }),
            h('form', {
                class: 'relative w-full max-w-lg rounded-lg bg-white p-5 shadow-2xl sm:p-6',
                onSubmit: (event: Event) => {
                    event.preventDefault();
                    emit('submit', selectedDays.value);
                },
            }, [
                h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-amber-600' }, 'Featured listing'),
                h('h2', { class: 'mt-1 text-2xl font-semibold text-slate-950' }, modalProps.listing.title),
                h('label', { class: 'mt-5 block' }, [
                    h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, 'Featured duration'),
                    h('select', {
                        class: 'admin-input',
                        value: selectedDays.value,
                        onChange: (event: Event) => { selectedDays.value = Number((event.target as HTMLSelectElement).value); },
                    }, allowed.length
                        ? allowed.map((plan) => h('option', { value: plan.duration_days }, `${plan.name} (${plan.duration_days} Days)`))
                        : [7, 14, 30].map((days) => h('option', { value: days }, `${days} Days`))),
                ]),
                h('div', { class: 'mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end' }, [
                    h('button', { type: 'button', class: 'rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700', onClick: () => emit('close') }, 'Cancel'),
                    h('button', { type: 'submit', class: 'rounded-lg bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600' }, 'Make Featured'),
                ]),
            ]),
        ]);
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
