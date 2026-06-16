<template>
    <Head :title="`${listing.title} - Customer Listing`" />

    <AdminLayout :title="listing.title" eyebrow="Customer listing details">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link href="/admin/customer-listings" class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Back to listings
                    </Link>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-semibold text-slate-950">{{ listing.title }}</h2>
                        <StatusBadge :status="listing.status" />
                        <span v-if="listing.is_featured" class="inline-flex rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                            Featured
                        </span>
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        #{{ listing.unique_id }} / listed {{ listing.created_at || 'N/A' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link :href="listing.actions.edit" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Edit
                    </Link>
                    <button v-if="listing.status === 'pending'" type="button" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white" @click="approveListing">
                        Approve
                    </button>
                    <button v-if="listing.status === 'pending'" type="button" class="rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white" @click="showReject = true">
                        Reject
                    </button>
                    <button
                        v-if="!listing.is_featured"
                        type="button"
                        class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                        @click="showFeature = true"
                    >
                        Make Featured
                    </button>
                    <button
                        v-else-if="!listing.paid_featured_active"
                        type="button"
                        class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700"
                        @click="removeFeatured"
                    >
                        Remove Featured
                    </button>
                    <button type="button" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700" @click="deleteListing">
                        Delete
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Price" :value="formatCurrency(listing.price)" tone="teal" />
                <MetricTile label="Year" :value="String(listing.year || 'N/A')" />
                <MetricTile label="KM Driven" :value="listing.km_driven ? `${formatNumber(listing.km_driven)} km` : 'N/A'" />
                <MetricTile label="City" :value="listing.city || 'N/A'" tone="orange" />
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.75fr]">
            <div class="grid gap-5">
                <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Gallery</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-950">Listing images</h3>
                    </div>
                    <div v-if="listing.images.length" class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4">
                        <a v-for="image in listing.images" :key="image.path" :href="image.url" target="_blank" rel="noreferrer">
                            <img :src="image.url" alt="" class="aspect-[4/3] w-full rounded-lg object-cover" />
                        </a>
                    </div>
                    <div v-else class="px-5 py-14 text-center">
                        <p class="text-lg font-semibold text-slate-950">No images uploaded</p>
                        <p class="mt-2 text-sm font-semibold text-slate-500">Add images from the edit screen.</p>
                    </div>
                </section>

                <Panel title="Car details" eyebrow="Specification">
                    <InfoList
                        :items="[
                            ['Brand', listing.brand || 'N/A'],
                            ['Model', listing.model || 'N/A'],
                            ['Fuel Type', formatSpec(listing.fuel_type)],
                            ['Transmission', formatSpec(listing.transmission)],
                            ['Owners', listing.owners || 'N/A'],
                            ['Registration', listing.registration_number || 'N/A'],
                        ]"
                    />
                    <div v-if="listing.status === 'rejected' && listing.rejection_reason" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ listing.rejection_reason }}
                    </div>
                </Panel>
            </div>

            <div class="grid gap-5 content-start">
                <Panel title="Owner details" eyebrow="Seller">
                    <p class="text-lg font-semibold text-slate-950">{{ listing.owner_name || 'N/A' }}</p>
                    <a v-if="listing.owner_phone" :href="`tel:${listing.owner_phone}`" class="mt-2 inline-flex text-sm font-semibold text-teal-700">{{ listing.owner_phone }}</a>
                    <a
                        v-if="listing.whatsapp_number"
                        :href="`https://wa.me/${cleanPhone(listing.whatsapp_number)}`"
                        target="_blank"
                        rel="noreferrer"
                        class="mt-2 block text-sm font-semibold text-teal-700"
                    >
                        WhatsApp: {{ listing.whatsapp_number }}
                    </a>
                </Panel>

                <Panel title="Location" eyebrow="Map">
                    <InfoList
                        :items="[
                            ['City', listing.city || 'N/A'],
                            ['Latitude', listing.latitude || 'N/A'],
                            ['Longitude', listing.longitude || 'N/A'],
                        ]"
                    />
                    <a v-if="listing.map_url" :href="listing.map_url" target="_blank" rel="noreferrer" class="mt-4 inline-flex rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white">
                        Open in Maps
                    </a>
                </Panel>

                <Panel title="Featured state" eyebrow="Promotion">
                    <InfoList
                        :items="[
                            ['Featured', listing.is_featured ? 'Yes' : 'No'],
                            ['Expires', listing.featured_expires_at || 'N/A'],
                            ['Paid Plan', listing.paid_featured_active ? 'Active' : 'No'],
                        ]"
                    />
                </Panel>
            </div>
        </section>

        <ModalShell v-if="showReject" title="Reject listing" eyebrow="Moderation" @close="showReject = false">
            <form @submit.prevent="rejectListing">
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Reason for rejection</span>
                    <textarea v-model="rejectForm.rejection_reason" class="admin-input min-h-32" required></textarea>
                </label>
                <p v-if="firstError(rejectForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(rejectForm) }}</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700" @click="showReject = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white" :disabled="rejectForm.processing">
                        {{ rejectForm.processing ? 'Rejecting...' : 'Reject Listing' }}
                    </button>
                </div>
            </form>
        </ModalShell>

        <ModalShell v-if="showFeature" title="Make featured" eyebrow="Promotion" @close="showFeature = false">
            <form @submit.prevent="featureListing">
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Featured duration</span>
                    <select v-model="featureDays" class="admin-input">
                        <option v-for="plan in allowedPlans" :key="plan.id" :value="plan.duration_days">{{ plan.name }} ({{ plan.duration_days }} Days)</option>
                        <option v-if="!allowedPlans.length" :value="7">7 Days</option>
                    </select>
                </label>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700" @click="showFeature = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600">
                        Make Featured
                    </button>
                </div>
            </form>
        </ModalShell>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type FeaturedPlan = { id: number; name: string; duration_days: number };
type Listing = {
    title: string;
    unique_id: string;
    brand?: string | null;
    model?: string | null;
    year?: number | null;
    fuel_type?: string | null;
    transmission?: string | null;
    km_driven?: number | null;
    price: number;
    city?: string | null;
    latitude?: number | string | null;
    longitude?: number | string | null;
    registration_number?: string | null;
    owners?: number | null;
    owner_name?: string | null;
    owner_phone?: string | null;
    whatsapp_number?: string | null;
    status: string;
    rejection_reason?: string | null;
    is_featured: boolean;
    featured_expires_at?: string | null;
    paid_featured_active: boolean;
    map_url?: string | null;
    created_at?: string | null;
    images: Array<{ path: string; url: string }>;
    actions: Record<string, string>;
};

const props = defineProps<{
    listing: Listing;
    featuredPlans: FeaturedPlan[];
}>();

const showReject = ref(false);
const showFeature = ref(false);
const allowedPlans = computed(() => props.featuredPlans.filter((plan) => [7, 14, 30].includes(plan.duration_days)));
const featureDays = ref(allowedPlans.value[0]?.duration_days || 7);
const rejectForm = useForm({ rejection_reason: '' });

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const formatNumber = (value: number | string) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const formatSpec = (value?: string | null) => value ? value.charAt(0).toUpperCase() + value.slice(1) : 'N/A';
const firstError = (form: any) => String(Object.values(form.errors)[0] || '');
const cleanPhone = (phone: string) => phone.replace(/\D/g, '');

const approveListing = () => router.post(props.listing.actions.approve, {}, { preserveScroll: true });
const rejectListing = () => {
    rejectForm.post(props.listing.actions.reject, {
        preserveScroll: true,
        onSuccess: () => {
            rejectForm.reset();
            showReject.value = false;
        },
    });
};
const featureListing = () => {
    router.post(props.listing.actions.featured, { days: featureDays.value }, {
        preserveScroll: true,
        onSuccess: () => { showFeature.value = false; },
    });
};
const removeFeatured = () => {
    if (window.confirm('Remove featured status from this listing?')) {
        router.post(props.listing.actions.remove_featured, {}, { preserveScroll: true });
    }
};
const deleteListing = () => {
    if (window.confirm('Delete this listing?')) {
        router.delete(props.listing.actions.destroy, { preserveScroll: true });
    }
};

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const classes = () => {
            if (tileProps.tone === 'teal') return 'border-teal-100 bg-teal-50 text-teal-700';
            if (tileProps.tone === 'orange') return 'border-orange-100 bg-orange-50 text-orange-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };
        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-semibold' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const Panel = defineComponent({
    props: {
        title: { type: String, required: true },
        eyebrow: { type: String, required: true },
    },
    setup(panelProps, { slots }) {
        return () => h('section', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-teal-700' }, panelProps.eyebrow),
            h('h3', { class: 'mt-1 text-xl font-semibold text-slate-950' }, panelProps.title),
            h('div', { class: 'mt-4' }, slots.default?.()),
        ]);
    },
});

const InfoList = defineComponent({
    props: {
        items: { type: Array as () => Array<[string, string | number | null | undefined]>, required: true },
    },
    setup(infoProps) {
        return () => h('dl', { class: 'grid gap-3' }, infoProps.items.map(([label, value]) => h('div', { class: 'grid gap-1 sm:grid-cols-[140px_1fr]' }, [
            h('dt', { class: 'text-xs font-semibold uppercase tracking-wide text-slate-500' }, label),
            h('dd', { class: 'break-words text-sm font-bold text-slate-800' }, value || 'N/A'),
        ])));
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

const ModalShell = defineComponent({
    props: {
        title: { type: String, required: true },
        eyebrow: { type: String, required: true },
    },
    emits: ['close'],
    setup(modalProps, { emit, slots }) {
        return () => h('div', { class: 'fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4' }, [
            h('button', { class: 'absolute inset-0', type: 'button', 'aria-label': 'Close modal', onClick: () => emit('close') }),
            h('div', { class: 'relative w-full max-w-xl rounded-lg bg-white p-5 shadow-2xl sm:p-6' }, [
                h('div', { class: 'flex items-start justify-between gap-3' }, [
                    h('div', [
                        h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-teal-700' }, modalProps.eyebrow),
                        h('h2', { class: 'mt-1 text-2xl font-semibold text-slate-950' }, modalProps.title),
                    ]),
                    h('button', { type: 'button', class: 'rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600', onClick: () => emit('close') }, 'Close'),
                ]),
                h('div', { class: 'mt-5' }, slots.default?.()),
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
