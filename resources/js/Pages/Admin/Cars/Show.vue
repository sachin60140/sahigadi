<template>
    <Head :title="`${car.title} - Car Details`" />

    <AdminLayout :title="car.title" eyebrow="Inventory details">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link href="/admin/cars" class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                        Back to cars
                    </Link>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-black text-slate-950">{{ car.title }}</h2>
                        <StatusBadge :status="car.status" />
                        <span v-if="car.is_featured" class="inline-flex rounded-md bg-amber-50 px-2.5 py-1 text-xs font-black text-amber-700 ring-1 ring-amber-100">
                            Featured
                        </span>
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        #{{ car.unique_id }} / {{ car.brand || 'No brand' }} / {{ car.model || 'No model' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link :href="car.actions.edit" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600">
                        Edit
                    </Link>
                    <button v-if="car.status === 'pending'" type="button" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-black text-white" @click="approveCar">
                        Approve
                    </button>
                    <button v-if="car.status === 'pending'" type="button" class="rounded-lg bg-red-600 px-4 py-3 text-sm font-black text-white" @click="showReject = true">
                        Reject
                    </button>
                    <button
                        v-if="!car.is_featured"
                        type="button"
                        class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-black text-amber-700"
                        @click="showFeature = true"
                    >
                        Make Featured
                    </button>
                    <button
                        v-else-if="!car.paid_featured_active"
                        type="button"
                        class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-black text-slate-700"
                        @click="removeFeatured"
                    >
                        Remove Featured
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Price" :value="formatCurrency(car.price)" tone="teal" />
                <MetricTile label="Year" :value="String(car.year || 'N/A')" />
                <MetricTile label="KM Driven" :value="car.km_driven ? `${formatNumber(car.km_driven)} km` : 'N/A'" />
                <MetricTile label="City" :value="car.city || 'N/A'" tone="orange" />
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.75fr]">
            <div class="grid gap-5">
                <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">Gallery</p>
                        <h3 class="mt-1 text-xl font-black text-slate-950">Listing images</h3>
                    </div>
                    <div v-if="car.images.length" class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4">
                        <img v-for="image in car.images" :key="image.id" :src="image.url" alt="" class="aspect-[4/3] w-full rounded-lg object-cover" />
                    </div>
                    <div v-else class="px-5 py-14 text-center">
                        <p class="text-lg font-black text-slate-950">No images uploaded</p>
                        <p class="mt-2 text-sm font-semibold text-slate-500">Add images from the edit screen.</p>
                    </div>
                </section>

                <Panel title="Vehicle details" eyebrow="Specification">
                    <InfoList
                        :items="[
                            ['Brand', car.brand || 'N/A'],
                            ['Model', car.model || 'N/A'],
                            ['Fuel Type', formatSpec(car.fuel_type)],
                            ['Transmission', formatSpec(car.transmission)],
                            ['Owners', car.owners || 'N/A'],
                            ['Registration', car.registration_number || 'N/A'],
                            ['Created', car.created_at || 'N/A'],
                        ]"
                    />
                    <div v-if="car.description" class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Description</p>
                        <p class="mt-2 text-sm font-semibold leading-7 text-slate-700">{{ car.description }}</p>
                    </div>
                </Panel>
            </div>

            <div class="grid gap-5 content-start">
                <Panel title="Dealer" eyebrow="Seller">
                    <p class="text-lg font-black text-slate-950">{{ car.dealer.name || 'N/A' }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ car.dealer.email || 'No email' }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ car.dealer.phone || 'No phone' }}</p>
                    <Link v-if="car.dealer.show_url" :href="car.dealer.show_url" class="mt-4 inline-flex rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-700">
                        View Dealer
                    </Link>
                </Panel>

                <Panel title="Location" eyebrow="Map">
                    <InfoList
                        :items="[
                            ['City', car.city || 'N/A'],
                            ['Latitude', car.latitude || 'N/A'],
                            ['Longitude', car.longitude || 'N/A'],
                        ]"
                    />
                    <a v-if="car.map_url" :href="car.map_url" target="_blank" rel="noreferrer" class="mt-4 inline-flex rounded-lg bg-teal-700 px-4 py-2 text-sm font-black text-white">
                        Open in Maps
                    </a>
                </Panel>

                <Panel title="Featured state" eyebrow="Promotion">
                    <InfoList
                        :items="[
                            ['Featured', car.is_featured ? 'Yes' : 'No'],
                            ['Expires', car.featured_expires_at || 'N/A'],
                            ['Paid Plan', car.paid_featured_active ? 'Active' : 'No'],
                        ]"
                    />
                </Panel>
            </div>
        </section>

        <ModalShell v-if="showReject" title="Reject car" eyebrow="Moderation" @close="showReject = false">
            <form @submit.prevent="rejectCar">
                <label class="block">
                    <span class="mb-2 block text-sm font-black text-slate-700">Reason for rejection</span>
                    <textarea v-model="rejectForm.rejection_reason" class="admin-input min-h-32" required></textarea>
                </label>
                <p v-if="firstError(rejectForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(rejectForm) }}</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-black text-slate-700" @click="showReject = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white" :disabled="rejectForm.processing">
                        {{ rejectForm.processing ? 'Rejecting...' : 'Reject Car' }}
                    </button>
                </div>
            </form>
        </ModalShell>

        <ModalShell v-if="showFeature" title="Make featured" eyebrow="Promotion" @close="showFeature = false">
            <form @submit.prevent="featureCar">
                <label class="block">
                    <span class="mb-2 block text-sm font-black text-slate-700">Featured duration</span>
                    <select v-model="featureDays" class="admin-input">
                        <option v-for="plan in featuredPlans" :key="plan.id" :value="plan.duration_days">{{ plan.name }} ({{ plan.duration_days }} Days)</option>
                        <option v-if="!featuredPlans.length" :value="7">7 Days</option>
                    </select>
                </label>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-black text-slate-700" @click="showFeature = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-amber-500 px-5 py-3 text-sm font-black text-white hover:bg-amber-600">
                        Make Featured
                    </button>
                </div>
            </form>
        </ModalShell>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type FeaturedPlan = { id: number; name: string; duration_days: number };
type Car = {
    id: number;
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
    status: string;
    description?: string | null;
    is_featured: boolean;
    featured_expires_at?: string | null;
    paid_featured_active: boolean;
    map_url?: string | null;
    created_at?: string | null;
    dealer: { name?: string | null; email?: string | null; phone?: string | null; show_url?: string | null };
    images: Array<{ id: number; url: string }>;
    actions: Record<string, string>;
};

const props = defineProps<{
    car: Car;
    featuredPlans: FeaturedPlan[];
}>();

const showReject = ref(false);
const showFeature = ref(false);
const featureDays = ref(props.featuredPlans[0]?.duration_days || 7);
const rejectForm = useForm({ rejection_reason: '' });

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const formatNumber = (value: number | string) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const formatSpec = (value?: string | null) => value ? value.charAt(0).toUpperCase() + value.slice(1) : 'N/A';
const firstError = (form: any) => String(Object.values(form.errors)[0] || '');

const approveCar = () => router.post(props.car.actions.approve, {}, { preserveScroll: true });
const rejectCar = () => {
    rejectForm.post(props.car.actions.reject, {
        preserveScroll: true,
        onSuccess: () => {
            rejectForm.reset();
            showReject.value = false;
        },
    });
};
const featureCar = () => {
    router.post(props.car.actions.featured, { days: featureDays.value }, {
        preserveScroll: true,
        onSuccess: () => { showFeature.value = false; },
    });
};
const removeFeatured = () => {
    if (window.confirm('Remove featured status from this car?')) {
        router.post(props.car.actions.remove_featured, {}, { preserveScroll: true });
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
            h('p', { class: 'text-2xl font-black' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
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
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-teal-700' }, panelProps.eyebrow),
            h('h3', { class: 'mt-1 text-xl font-black text-slate-950' }, panelProps.title),
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
            h('dt', { class: 'text-xs font-black uppercase tracking-wide text-slate-500' }, label),
            h('dd', { class: 'break-words text-sm font-bold text-slate-800' }, value || 'N/A'),
        ])));
    },
});

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex w-fit rounded-md px-2.5 py-1 text-xs font-black capitalize',
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
                        h('p', { class: 'text-xs font-black uppercase tracking-wide text-teal-700' }, modalProps.eyebrow),
                        h('h2', { class: 'mt-1 text-2xl font-black text-slate-950' }, modalProps.title),
                    ]),
                    h('button', { type: 'button', class: 'rounded-lg border border-slate-200 px-3 py-2 text-sm font-black text-slate-600', onClick: () => emit('close') }, 'Close'),
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
