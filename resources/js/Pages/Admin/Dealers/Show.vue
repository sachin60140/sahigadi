<template>
    <Head :title="`${dealer.name} - Dealer Details`" />

    <AdminLayout :title="dealer.name" eyebrow="Dealer details">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link :href="dealer.actions.back" class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Back to dealers
                    </Link>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-semibold text-slate-950">{{ dealer.name }}</h2>
                        <StatusBadge :status="dealer.status" />
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ dealer.email }} <span v-if="dealer.phone">/ {{ dealer.phone }}</span></p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ dealer.dealer_unique_id }} / {{ dealer.company_name || 'Individual dealer' }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link :href="dealer.actions.edit" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Edit Dealer
                    </Link>
                    <button
                        v-if="dealer.status === 'pending'"
                        type="button"
                        class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800"
                        @click="approveDealer"
                    >
                        Approve
                    </button>
                    <button
                        v-if="dealer.status === 'pending'"
                        type="button"
                        class="rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                        @click="showReject = true"
                    >
                        Reject
                    </button>
                    <button
                        v-if="dealer.status !== 'pending'"
                        type="button"
                        class="rounded-lg border px-4 py-3 text-sm font-semibold transition"
                        :class="dealer.status === 'approved' ? 'border-red-200 bg-red-50 text-red-700 hover:bg-white' : 'border-teal-200 bg-teal-50 text-teal-700 hover:bg-white'"
                        @click="toggleDealer"
                    >
                        {{ dealer.status === 'approved' ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Wallet balance" :value="formatCurrency(dealer.wallet_balance)" tone="teal" />
                <MetricTile label="Listed cars" :value="String(dealer.cars.length)" />
                <MetricTile label="Profile completion" :value="`${dealer.profile_completion}%`" tone="orange" />
                <MetricTile label="GST state" :value="dealer.gst_verified ? 'Verified' : 'Pending'" :tone="dealer.gst_verified ? 'teal' : 'red'" />
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[0.9fr_1.4fr]">
            <div class="grid gap-5">
                <Panel title="Basic information" eyebrow="Profile">
                    <InfoList
                        :items="[
                            ['Name', dealer.name],
                            ['Email', dealer.email],
                            ['Phone', dealer.phone || 'N/A'],
                            ['Company', dealer.company_name || 'N/A'],
                            ['City', dealer.city || 'N/A'],
                            ['State', dealer.state || 'N/A'],
                            ['Pincode', dealer.pincode || 'N/A'],
                            ['Address', dealer.address || 'N/A'],
                            ['Joined', dealer.joined_at || 'N/A'],
                        ]"
                    />
                </Panel>

                <Panel title="KYC documents" eyebrow="Verification">
                    <InfoList
                        :items="[
                            ['Aadhaar Number', dealer.kyc_document_number || 'N/A'],
                            ['PAN Number', dealer.pan_number || 'N/A'],
                        ]"
                    />
                    <div class="mt-4 grid gap-3">
                        <DocumentRow label="Aadhaar file" :url="dealer.documents.kyc" />
                        <DocumentRow label="PAN file" :url="dealer.documents.pan" />
                    </div>
                </Panel>

                <Panel v-if="dealer.gst_number" title="GST documents" eyebrow="Tax profile">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <InfoList
                            class="flex-1"
                            :items="[
                                ['GST Number', dealer.gst_number || 'N/A'],
                                ['Verified On', dealer.gst_verified_at || 'Not verified'],
                            ]"
                        />
                        <div class="flex gap-2">
                            <button
                                v-if="dealer.documents.gst && !dealer.gst_verified"
                                type="button"
                                class="rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white"
                                @click="verifyGst"
                            >
                                Verify GST
                            </button>
                            <button
                                v-if="dealer.gst_verified"
                                type="button"
                                class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-700"
                                @click="unverifyGst"
                            >
                                Unverify
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <DocumentRow label="GST file" :url="dealer.documents.gst" />
                    </div>
                </Panel>
            </div>

            <div class="grid gap-5">
                <Panel title="Wallet operations" eyebrow="Finance">
                    <div class="grid gap-4 lg:grid-cols-2">
                        <form class="rounded-lg border border-teal-100 bg-teal-50 p-4" @submit.prevent="creditWallet">
                            <h3 class="font-semibold text-teal-800">Add money</h3>
                            <div class="mt-3 grid gap-3">
                                <input v-model="creditForm.amount" class="admin-input" min="1" required step="0.01" type="number" placeholder="Amount" />
                                <input v-model="creditForm.remark" class="admin-input" type="text" placeholder="Remark optional" />
                            </div>
                            <p v-if="firstError(creditForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(creditForm) }}</p>
                            <button type="submit" class="mt-4 w-full rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white" :disabled="creditForm.processing">
                                {{ creditForm.processing ? 'Adding...' : 'Add Money' }}
                            </button>
                        </form>

                        <form class="rounded-lg border border-red-100 bg-red-50 p-4" @submit.prevent="debitWallet">
                            <h3 class="font-semibold text-red-800">Debit money</h3>
                            <div class="mt-3 grid gap-3">
                                <input v-model="debitForm.amount" class="admin-input" min="1" required step="0.01" type="number" placeholder="Amount" />
                                <input v-model="debitForm.remark" class="admin-input" type="text" placeholder="Remark optional" />
                            </div>
                            <p v-if="firstError(debitForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(debitForm) }}</p>
                            <button type="submit" class="mt-4 w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white" :disabled="debitForm.processing">
                                {{ debitForm.processing ? 'Debiting...' : 'Debit Money' }}
                            </button>
                        </form>
                    </div>
                </Panel>

                <Panel title="Subscriptions" eyebrow="Plans">
                    <div v-if="dealer.subscriptions.length" class="grid gap-3">
                        <div v-for="subscription in dealer.subscriptions" :key="subscription.id" class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ subscription.plan_name }}</p>
                                    <p class="mt-1 text-xs font-bold text-slate-500">
                                        {{ subscription.active_listings }}/{{ subscription.listing_limit }} listings / expires {{ subscription.expires_at || 'N/A' }}
                                    </p>
                                </div>
                                <span class="inline-flex w-fit rounded-md px-2.5 py-1 text-xs font-semibold" :class="subscription.is_active ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-slate-200 text-slate-600'">
                                    {{ subscription.is_active ? 'Active' : 'Expired' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <EmptyLine v-else text="No active subscription found." />

                    <form class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]" @submit.prevent="assignPlan">
                        <select v-model="planForm.plan_id" class="admin-input" required>
                            <option value="">Select plan</option>
                            <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                {{ plan.name }} - {{ formatCurrency(plan.price) }} / {{ plan.listing_limit }} listings
                            </option>
                        </select>
                        <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700" :disabled="planForm.processing">
                            {{ planForm.processing ? 'Assigning...' : 'Assign Plan' }}
                        </button>
                    </form>
                    <p v-if="firstError(planForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(planForm) }}</p>
                </Panel>

                <Panel title="Cars" :eyebrow="`${dealer.cars.length} listings`">
                    <div v-if="dealer.cars.length" class="overflow-x-auto">
                        <table class="min-w-[640px] w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Car</th>
                                    <th class="px-4 py-3">Price</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="car in dealer.cars" :key="car.id">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-950">{{ car.title }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ car.year || 'N/A' }} / {{ car.fuel_type || 'N/A' }} / {{ car.transmission || 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-950">{{ formatCurrency(car.price) }}</td>
                                    <td class="px-4 py-3"><StatusBadge :status="car.status" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <EmptyLine v-else text="No cars listed." />
                </Panel>

                <Panel title="Wallet transactions" eyebrow="Recent movement">
                    <div v-if="walletTransactions.length" class="overflow-x-auto">
                        <table class="min-w-[720px] w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="transaction in walletTransactions" :key="transaction.id">
                                    <td class="px-4 py-3 font-semibold text-slate-600">{{ transaction.created_at || 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold capitalize" :class="transaction.type === 'credit' ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'">
                                            {{ transaction.type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold" :class="transaction.type === 'credit' ? 'text-teal-700' : 'text-red-700'">
                                        {{ transaction.type === 'credit' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ transaction.remark || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <EmptyLine v-else text="No wallet transactions found." />
                </Panel>
            </div>
        </section>

        <div v-if="showReject" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4">
            <button class="absolute inset-0" type="button" aria-label="Close modal" @click="showReject = false"></button>
            <form class="relative w-full max-w-xl rounded-lg bg-white p-5 shadow-2xl sm:p-6" @submit.prevent="rejectDealer">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Dealer review</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-950">Reject dealer</h2>
                    </div>
                    <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600" @click="showReject = false">Close</button>
                </div>
                <label class="mt-5 block">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Reason for rejection</span>
                    <textarea v-model="rejectForm.rejection_reason" class="admin-input min-h-32" required></textarea>
                </label>
                <p v-if="firstError(rejectForm)" class="mt-2 text-xs font-bold text-red-700">{{ firstError(rejectForm) }}</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700" @click="showReject = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white" :disabled="rejectForm.processing">
                        {{ rejectForm.processing ? 'Rejecting...' : 'Reject Dealer' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Dealer = {
    id: number;
    dealer_unique_id?: string | null;
    name: string;
    email: string;
    phone?: string | null;
    company_name?: string | null;
    city?: string | null;
    state?: string | null;
    pincode?: string | null;
    address?: string | null;
    status: string;
    wallet_balance: number;
    gst_number?: string | null;
    gst_verified: boolean;
    gst_verified_at?: string | null;
    joined_at?: string | null;
    kyc_document_number?: string | null;
    pan_number?: string | null;
    profile_completion: number;
    documents: { kyc?: string | null; pan?: string | null; gst?: string | null };
    cars: Array<{ id: number; title: string; price: number; status: string; year?: number | null; fuel_type?: string | null; transmission?: string | null }>;
    subscriptions: Array<{ id: number; plan_name: string; listing_limit: number; active_listings: number; expires_at?: string | null; is_active: boolean }>;
    actions: Record<string, string>;
};

type WalletTransaction = { id: number; type: string; amount: number; remark?: string | null; created_at?: string | null };
type Plan = { id: number; name: string; price: number; listing_limit: number };

const props = defineProps<{
    dealer: Dealer;
    walletTransactions: WalletTransaction[];
    plans: Plan[];
}>();

const showReject = ref(false);
const creditForm = useForm({ amount: '', remark: '' });
const debitForm = useForm({ amount: '', remark: '' });
const planForm = useForm({ plan_id: '' });
const rejectForm = useForm({ rejection_reason: '' });

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const firstError = (form: any) => String(Object.values(form.errors)[0] || '');

const approveDealer = () => {
    router.post(props.dealer.actions.approve, {}, { preserveScroll: true });
};

const toggleDealer = () => {
    const message = props.dealer.status === 'approved' ? 'Deactivate this dealer?' : 'Activate this dealer?';
    if (window.confirm(message)) {
        router.post(props.dealer.actions.toggle_status, {}, { preserveScroll: true });
    }
};

const rejectDealer = () => {
    rejectForm.post(props.dealer.actions.reject, {
        preserveScroll: true,
        onSuccess: () => {
            rejectForm.reset();
            showReject.value = false;
        },
    });
};

const creditWallet = () => {
    creditForm.post(props.dealer.actions.add_money, {
        preserveScroll: true,
        onSuccess: () => creditForm.reset(),
    });
};

const debitWallet = () => {
    if (!window.confirm('Debit this amount from dealer wallet?')) {
        return;
    }
    debitForm.post(props.dealer.actions.debit_money, {
        preserveScroll: true,
        onSuccess: () => debitForm.reset(),
    });
};

const assignPlan = () => {
    planForm.post(props.dealer.actions.assign_plan, {
        preserveScroll: true,
        onSuccess: () => planForm.reset(),
    });
};

const verifyGst = () => router.post(props.dealer.actions.verify_gst, {}, { preserveScroll: true });
const unverifyGst = () => {
    if (window.confirm('Remove GST verification?')) {
        router.post(props.dealer.actions.unverify_gst, {}, { preserveScroll: true });
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
            if (tileProps.tone === 'red') return 'border-red-100 bg-red-50 text-red-700';
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
        class: { type: String, default: '' },
    },
    setup(infoProps) {
        return () => h('dl', { class: ['grid gap-3', infoProps.class] }, infoProps.items.map(([label, value]) => h('div', { class: 'grid gap-1 sm:grid-cols-[140px_1fr]' }, [
            h('dt', { class: 'text-xs font-semibold uppercase tracking-wide text-slate-500' }, label),
            h('dd', { class: 'break-words text-sm font-bold text-slate-800' }, value || 'N/A'),
        ])));
    },
});

const DocumentRow = defineComponent({
    props: {
        label: { type: String, required: true },
        url: { type: String, default: '' },
    },
    setup(documentProps) {
        return () => h('div', { class: 'flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3' }, [
            h('span', { class: 'text-sm font-semibold text-slate-700' }, documentProps.label),
            documentProps.url
                ? h('a', { href: documentProps.url, target: '_blank', rel: 'noreferrer', class: 'rounded-lg bg-slate-950 px-3 py-2 text-xs font-semibold text-white' }, 'View')
                : h('span', { class: 'text-xs font-bold text-slate-400' }, 'Not uploaded'),
        ]);
    },
});

const EmptyLine = defineComponent({
    props: { text: { type: String, required: true } },
    setup(emptyProps) {
        return () => h('p', { class: 'rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm font-bold text-slate-500' }, emptyProps.text);
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
</script>

<style scoped>
.admin-input {
    min-height: 48px;
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgb(226 232 240);
    background: white;
    padding: 12px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(30 41 59);
    outline: none;
}
.admin-input:focus {
    border-color: rgb(13 148 136);
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
