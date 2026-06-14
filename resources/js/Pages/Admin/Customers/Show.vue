<template>
    <Head :title="`${customer.name} - Customer Details`" />

    <AdminLayout :title="customer.name" eyebrow="Customer details">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link :href="customer.actions.back" class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                        Back to customers
                    </Link>
                    <h2 class="mt-5 text-3xl font-black text-slate-950">{{ customer.name }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ customer.email }} <span v-if="customer.phone">/ {{ customer.phone }}</span></p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ customer.customer_unique_id }} / {{ customer.company_name || 'Individual customer' }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link :href="customer.actions.edit" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600">
                        Edit Customer
                    </Link>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <MetricTile label="Wallet balance" :value="formatCurrency(customer.wallet_balance)" tone="teal" />
                <MetricTile label="Listings" :value="String(customer.listings.length)" />
                <MetricTile label="Profile completion" :value="`${customer.profile_completion_percentage}%`" :tone="customer.profile_completion_percentage >= 75 ? 'teal' : 'orange'" />
                <MetricTile label="City" :value="customer.city || 'N/A'" />
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[0.9fr_1.4fr]">
            <div class="grid gap-5">
                <Panel title="Basic information" eyebrow="Profile">
                    <InfoList
                        :items="[
                            ['Name', customer.name],
                            ['Email', customer.email],
                            ['Phone', customer.phone || 'N/A'],
                            ['WhatsApp', customer.whatsapp_number || 'N/A'],
                            ['Company', customer.company_name || 'N/A'],
                            ['City', customer.city || 'N/A'],
                            ['State', customer.state || 'N/A'],
                            ['Pincode', customer.pincode || 'N/A'],
                            ['Address', customer.address || 'N/A'],
                            ['Joined', customer.joined_at || 'N/A'],
                        ]"
                    />
                </Panel>

                <Panel title="Identity and GST" eyebrow="Verification">
                    <InfoList
                        :items="[
                            ['GST Number', customer.gst_number || 'N/A'],
                            ['Aadhaar Number', customer.aadhaar_number || 'N/A'],
                            ['PAN Number', customer.pan_number || 'N/A'],
                            ['Gender', customer.gender || 'N/A'],
                            ['Date of Birth', customer.dob || 'N/A'],
                        ]"
                    />
                </Panel>

                <Panel title="Profile health" eyebrow="Completion">
                    <ProfileBar :value="customer.profile_completion_percentage" />
                    <div v-if="customer.missing_profile_fields.length" class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="field in customer.missing_profile_fields"
                            :key="field"
                            class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-700 ring-1 ring-orange-100"
                        >
                            {{ field }}
                        </span>
                    </div>
                    <EmptyLine v-else class="mt-4" text="Profile has all important fields." />
                </Panel>
            </div>

            <div class="grid gap-5">
                <Panel title="Car listings" :eyebrow="`${customer.listings.length} customer listings`">
                    <div v-if="customer.listings.length" class="overflow-x-auto">
                        <table class="min-w-[760px] w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Vehicle</th>
                                    <th class="px-4 py-3">Year</th>
                                    <th class="px-4 py-3">Price</th>
                                    <th class="px-4 py-3">City</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="listing in customer.listings" :key="listing.id">
                                    <td class="px-4 py-3">
                                        <p class="font-black text-slate-950">{{ listing.title || 'Untitled vehicle' }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ listing.transmission || 'N/A' }} / {{ listing.fuel_type || 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-600">{{ listing.year || 'N/A' }}</td>
                                    <td class="px-4 py-3 font-black text-slate-950">{{ formatCurrency(listing.price) }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-600">{{ listing.city || 'N/A' }}</td>
                                    <td class="px-4 py-3"><StatusBadge :status="listing.status" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <EmptyLine v-else text="No cars listed by this customer." />
                </Panel>

                <Panel title="Wallet transactions" eyebrow="Recent movement">
                    <div v-if="customer.wallet_transactions.length" class="overflow-x-auto">
                        <table class="min-w-[720px] w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="transaction in customer.wallet_transactions" :key="transaction.id">
                                    <td class="px-4 py-3 font-semibold text-slate-600">{{ transaction.created_at || 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize" :class="transaction.type === 'credit' ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'">
                                            {{ transaction.type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-black" :class="transaction.type === 'credit' ? 'text-teal-700' : 'text-red-700'">
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
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Customer = {
    id: number;
    customer_unique_id?: string | null;
    name: string;
    email: string;
    phone?: string | null;
    whatsapp_number?: string | null;
    company_name?: string | null;
    city?: string | null;
    state?: string | null;
    pincode?: string | null;
    address?: string | null;
    gst_number?: string | null;
    aadhaar_number?: string | null;
    pan_number?: string | null;
    gender?: string | null;
    dob?: string | null;
    wallet_balance: number;
    profile_completion_percentage: number;
    missing_profile_fields: string[];
    joined_at?: string | null;
    listings: Array<{ id: number; title?: string | null; year?: number | null; fuel_type?: string | null; transmission?: string | null; price: number; city?: string | null; status: string }>;
    wallet_transactions: Array<{ id: number; type: string; amount: number; remark?: string | null; created_at?: string | null }>;
    actions: Record<string, string>;
};

defineProps<{ customer: Customer }>();

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

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

const ProfileBar = defineComponent({
    props: { value: { type: Number, required: true } },
    setup(barProps) {
        return () => h('div', [
            h('div', { class: 'h-3 overflow-hidden rounded-full bg-slate-100' }, [
                h('div', {
                    class: ['h-full rounded-full', barProps.value >= 75 ? 'bg-teal-600' : 'bg-orange-500'],
                    style: { width: `${Math.min(100, Math.max(0, barProps.value))}%` },
                }),
            ]),
            h('p', { class: 'mt-2 text-sm font-black text-slate-700' }, `${barProps.value}% completed`),
        ]);
    },
});

const EmptyLine = defineComponent({
    props: {
        text: { type: String, required: true },
        class: { type: String, default: '' },
    },
    setup(emptyProps) {
        return () => h('p', { class: ['rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm font-bold text-slate-500', emptyProps.class] }, emptyProps.text);
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
</script>
