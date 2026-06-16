<template>
    <Head title="My Dashboard" />
    <CustomerLayout title="My Dashboard" eyebrow="Listings and account">
        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <img v-if="account.profile_image_url" :src="account.profile_image_url" alt="" class="h-12 w-12 shrink-0 rounded-full object-cover" />
                <span v-else class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-teal-50 text-teal-700">
                    <UserRound class="h-6 w-6" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-base font-bold text-slate-950">{{ account.name || 'Welcome back' }}</p>
                    <p class="truncate text-xs font-medium text-slate-500">{{ account.customer_unique_id || account.phone }}</p>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all" :class="profilePct >= 100 ? 'bg-teal-600' : 'bg-amber-500'" :style="{ width: `${profilePct}%` }"></div>
                        </div>
                        <span class="shrink-0 text-xs font-semibold text-slate-500">{{ profilePct }}%</span>
                    </div>
                    <Link href="/customer/profile" class="mt-1 inline-flex text-xs font-semibold text-teal-700 hover:text-teal-800">
                        {{ profilePct >= 100 ? 'View profile' : 'Complete your profile' }}
                    </Link>
                </div>
            </div>

            <div class="flex flex-col justify-center rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <Wallet class="h-4 w-4" /> Wallet balance
                </p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ money(walletBalance) }}</p>
                <Link href="/customer/wallet" class="mt-3 inline-flex w-fit items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
                    Add money
                </Link>
            </div>
        </section>

        <section class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="All listings" :value="stats.total" :icon="CarFront" tone="slate" />
            <StatCard label="Approved" :value="stats.approved" :icon="BadgeCheck" tone="teal" />
            <StatCard label="Pending review" :value="stats.pending" :icon="Clock3" tone="orange" />
            <StatCard label="Featured" :value="stats.featured" :icon="Star" tone="amber" />
        </section>

        <section class="mt-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Your inventory</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-950">Car listings</h2>
                </div>
                <Link :href="actions.sellCar" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600">
                    <Plus class="h-4 w-4" />
                    Sell a car
                </Link>
            </div>

            <div v-if="listings.length" class="mt-5 grid gap-5 md:grid-cols-2 2xl:grid-cols-3">
                <article v-for="listing in listings" :key="listing.id" class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="relative aspect-[16/10] bg-slate-100">
                        <img v-if="listing.image_url" :src="listing.image_url" :alt="listing.title" class="h-full w-full object-cover" />
                        <div v-else class="grid h-full place-items-center text-slate-300"><CarFront class="h-14 w-14" /></div>
                        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                            <span class="rounded-md px-2.5 py-1 text-xs font-semibold capitalize" :class="statusClass(listing.status)">
                                {{ listing.status === 'pending' ? 'Pending review' : listing.status }}
                            </span>
                            <span v-if="listing.is_featured" class="inline-flex items-center gap-1 rounded-md bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                <Star class="h-3.5 w-3.5 fill-current" /> Featured
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-bold text-slate-950">{{ listing.title }}</h3>
                                <p class="mt-1 text-xs font-medium text-slate-500">{{ listing.unique_id }}</p>
                            </div>
                            <p class="shrink-0 text-lg font-bold text-teal-700">{{ money(listing.price) }}</p>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-bold text-slate-500">
                            <span class="truncate">{{ listing.year || 'Year N/A' }}</span>
                            <span class="truncate capitalize">{{ listing.fuel_type || 'Fuel N/A' }}</span>
                            <span class="truncate capitalize">{{ listing.transmission || 'Gear N/A' }}</span>
                        </div>
                        <p v-if="listing.km_driven" class="mt-3 text-sm font-semibold text-slate-500">{{ number(listing.km_driven) }} km driven</p>
                        <p v-if="listing.is_featured && listing.featured_expires_at" class="mt-3 text-xs font-semibold text-amber-700">
                            Featured until {{ listing.featured_expires_at }}
                        </p>
                        <p v-if="listing.status === 'rejected' && listing.rejection_reason" class="mt-4 rounded-lg border border-red-100 bg-red-50 p-3 text-sm font-bold text-red-700">
                            {{ listing.rejection_reason }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 border-t border-slate-100 p-4">
                        <a
                            v-if="listing.status === 'approved' && listing.view_url"
                            :href="listing.view_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            <Eye class="h-4 w-4" /> View
                        </a>
                        <Link
                            v-if="listing.status === 'approved'"
                            :href="listing.feature_url"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-100 px-3 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-200"
                        >
                            <Star class="h-4 w-4" /> {{ listing.is_featured ? 'Extend' : 'Feature' }}
                        </Link>
                        <Link
                            v-else
                            :href="listing.edit_url"
                            class="col-span-2 inline-flex items-center justify-center gap-2 rounded-lg border border-teal-200 bg-teal-50 px-3 py-2.5 text-sm font-semibold text-teal-700 hover:bg-teal-100"
                        >
                            <Pencil class="h-4 w-4" /> Edit listing
                        </Link>
                        <button
                            type="button"
                            class="col-span-2 inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50"
                            @click="openDelete(listing)"
                        >
                            <Trash2 class="h-4 w-4" /> Delete listing
                        </button>
                    </div>
                </article>
            </div>

            <div v-else class="mt-5 rounded-lg border border-dashed border-slate-300 bg-white px-5 py-14 text-center">
                <CarFront class="mx-auto h-12 w-12 text-slate-300" />
                <h3 class="mt-4 text-xl font-semibold text-slate-950">No cars listed yet</h3>
                <p class="mt-2 text-sm font-semibold text-slate-500">Create your first listing to start receiving buyer enquiries.</p>
                <Link :href="actions.sellCar" class="mt-5 inline-flex rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-800">
                    Sell your first car
                </Link>
            </div>
        </section>

        <div v-if="deleteListing" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4">
            <div class="w-full max-w-md rounded-lg bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Security verification</p>
                        <h2 class="mt-1 text-xl font-semibold text-slate-950">Delete listing</h2>
                    </div>
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-slate-600" aria-label="Close dialog" @click="closeDelete">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-sm font-semibold leading-6 text-slate-600">
                        Permanently delete <strong>{{ deleteListing.title }}</strong>. We will verify this action using your registered contact details.
                    </p>

                    <div v-if="deleteStep === 'send'" class="mt-5">
                        <p class="rounded-lg bg-slate-50 p-4 text-sm font-bold text-slate-600">
                            OTP will be sent to +91 {{ customer.phone }}<span v-if="customer.email"> and {{ customer.email }}</span>.
                        </p>
                        <button type="button" class="mt-4 w-full rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white disabled:opacity-50" :disabled="deleteBusy" @click="sendDeleteOtp">
                            {{ deleteBusy ? 'Sending...' : 'Send OTP' }}
                        </button>
                    </div>

                    <div v-else class="mt-5">
                        <label>
                            <span class="mb-2 block text-sm font-semibold text-slate-700">6-digit OTP</span>
                            <input v-model="deleteOtp" class="customer-input text-center text-xl tracking-[0.3em]" inputmode="numeric" maxlength="6" placeholder="------" />
                        </label>
                        <button type="button" class="mt-4 w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white disabled:opacity-50" :disabled="deleteBusy || deleteOtp.length !== 6" @click="confirmDelete">
                            {{ deleteBusy ? 'Verifying...' : 'Verify and delete' }}
                        </button>
                        <button type="button" class="mt-3 w-full text-sm font-semibold text-slate-500" :disabled="deleteBusy" @click="sendDeleteOtp">Resend OTP</button>
                    </div>

                    <p v-if="deleteError" class="mt-4 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ deleteError }}</p>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import {
    BadgeCheck,
    CarFront,
    Clock3,
    Eye,
    Pencil,
    Plus,
    Star,
    Trash2,
    UserRound,
    Wallet,
    X,
} from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

type Listing = {
    id: number;
    unique_id: string;
    title: string;
    year: number | null;
    fuel_type: string | null;
    transmission: string | null;
    km_driven: number | null;
    price: number;
    status: string;
    rejection_reason: string | null;
    image_url: string | null;
    is_featured: boolean;
    featured_expires_at: string | null;
    view_url: string | null;
    edit_url: string;
    feature_url: string;
    delete_url: string;
};

const props = defineProps<{
    customer: { phone: string; email: string | null };
    listings: Listing[];
    stats: { total: number; approved: number; pending: number; featured: number };
    actions: { sellCar: string; sendDeleteOtp: string };
}>();

const page = usePage();
const account = computed<any>(() => (page.props as any).auth?.customer || {});
const profilePct = computed(() => Math.min(100, Math.max(0, Math.round(Number(account.value.profile_completion_percentage || 0)))));
const walletBalance = computed(() => Number(account.value.wallet_balance || 0));

const deleteListing = ref<Listing | null>(null);
const deleteStep = ref<'send' | 'verify'>('send');
const deleteOtp = ref('');
const deleteBusy = ref(false);
const deleteError = ref('');

const number = (value: number) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const money = (value: number) => `Rs ${number(value)}`;
const statusClass = (status: string) => ({
    approved: 'bg-teal-100 text-teal-800',
    pending: 'bg-orange-100 text-orange-800',
    rejected: 'bg-red-100 text-red-800',
}[status] || 'bg-slate-100 text-slate-700');

const openDelete = (listing: Listing) => {
    deleteListing.value = listing;
    deleteStep.value = 'send';
    deleteOtp.value = '';
    deleteError.value = '';
};

const closeDelete = () => {
    if (deleteBusy.value) return;
    deleteListing.value = null;
};

const sendDeleteOtp = async () => {
    deleteBusy.value = true;
    deleteError.value = '';
    try {
        const { data } = await axios.post(props.actions.sendDeleteOtp);
        if (!data.success) throw new Error(data.message || 'Unable to send OTP.');
        deleteStep.value = 'verify';
    } catch (error: any) {
        deleteError.value = error?.response?.data?.message || error.message || 'Unable to send OTP.';
    } finally {
        deleteBusy.value = false;
    }
};

const confirmDelete = async () => {
    if (!deleteListing.value || !/^\d{6}$/.test(deleteOtp.value)) return;
    deleteBusy.value = true;
    deleteError.value = '';
    try {
        const { data } = await axios.delete(deleteListing.value.delete_url, { data: { otp: deleteOtp.value } });
        if (!data.success) throw new Error(data.message || 'Unable to delete listing.');
        deleteListing.value = null;
        router.reload({ only: ['listings', 'stats'] });
    } catch (error: any) {
        deleteError.value = error?.response?.data?.message || error.message || 'Unable to delete listing.';
    } finally {
        deleteBusy.value = false;
    }
};

const StatCard = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
        icon: { type: Object, required: true },
        tone: { type: String, required: true },
    },
    setup(p) {
        const tones: Record<string, string> = {
            slate: 'bg-slate-100 text-slate-700',
            teal: 'bg-teal-100 text-teal-700',
            orange: 'bg-orange-100 text-orange-700',
            amber: 'bg-amber-100 text-amber-700',
        };
        return () => h('article', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('div', { class: ['grid h-10 w-10 place-items-center rounded-lg', tones[p.tone]] }, [h(p.icon, { class: 'h-5 w-5' })]),
            h('p', { class: 'mt-4 text-3xl font-bold tracking-tight text-slate-950' }, String(p.value)),
            h('p', { class: 'mt-1 text-sm font-bold text-slate-500' }, p.label),
        ]);
    },
});
</script>
