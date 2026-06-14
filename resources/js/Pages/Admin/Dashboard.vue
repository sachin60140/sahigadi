<template>
    <Head title="Admin Dashboard" />

    <AdminLayout title="Admin Dashboard" eyebrow="Command center">
        <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-teal-700">Platform overview</p>
                        <h2 class="mt-2 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">
                            Revenue, approvals and service activity in one view.
                        </h2>
                        <p class="mt-3 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                            Track marketplace health across dealer inventory, customer listings, wallet movement, gateway status and paid service lookups.
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Last refreshed</p>
                        <p class="mt-1 text-sm font-black text-slate-950">{{ generatedAt }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 rounded-lg border border-slate-200 bg-slate-950 p-5 text-white shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-teal-200">Gateway health</p>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <GatewayStatus label="Razorpay" :active="gatewayHealth.razorpay_active" />
                    <GatewayStatus label="PhonePe" :active="gatewayHealth.phonepe_active" :meta="gatewayHealth.phonepe_environment" />
                </div>
                <div class="grid grid-cols-2 gap-3 border-t border-white/10 pt-3">
                    <MiniMetric label="Dealer min" :value="formatCurrency(gatewayHealth.dealer_min_recharge)" />
                    <MiniMetric label="Customer min" :value="formatCurrency(gatewayHealth.customer_min_recharge)" />
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <KpiCard
                label="Total completed payments"
                :value="formatCurrency(stats.payment_total)"
                helper="All completed gateway payments"
                tone="teal"
                href="/admin/wallet-recharges"
            />
            <KpiCard
                label="Last 30 days"
                :value="formatCurrency(stats.payment_month)"
                :helper="`${formatCurrency(stats.payment_today)} today`"
                tone="orange"
                href="/admin/customer-wallet-recharges"
            />
            <KpiCard
                label="Pending actions"
                :value="formatNumber(stats.pending_actions)"
                helper="Dealers, listings and unread support"
                tone="red"
                href="/admin/dealers"
            />
            <KpiCard
                label="Active inventory"
                :value="formatNumber(stats.approved_cars + stats.approved_customer_listings)"
                helper="Dealer cars and customer listings"
                tone="sky"
                href="/admin/cars"
            />
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600">Payment gateways</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-950">Gateway collection split</h2>
                    </div>
                    <Link href="/admin/payment-settings" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700 sm:w-fit">
                        Manage Settings
                    </Link>
                </div>

                <div class="mt-5 grid gap-3">
                    <div v-for="gateway in normalizedGateways" :key="gateway.gateway" class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-black text-slate-950">{{ gateway.gateway }}</h3>
                                <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ formatNumber(gateway.count) }} completed payments</p>
                            </div>
                            <p class="text-lg font-black text-slate-950">{{ formatCurrency(gateway.amount) }}</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-teal-600" :style="{ width: `${gateway.percent}%` }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Payment links</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950">Custom collection links</h2>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <StatusTile label="Total links" :value="formatNumber(paymentLinks.total)" />
                    <StatusTile label="Paid" :value="formatNumber(paymentLinks.paid)" tone="teal" />
                    <StatusTile label="Pending" :value="formatNumber(paymentLinks.pending)" tone="orange" />
                    <StatusTile label="Expired" :value="formatNumber(paymentLinks.expired)" tone="red" />
                </div>
                <div class="mt-4 rounded-lg border border-orange-100 bg-orange-50 p-4">
                    <p class="text-xs font-black uppercase tracking-wide text-orange-700">Pending value</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ formatCurrency(paymentLinks.pending_amount) }}</p>
                </div>
                <Link href="/admin/payment-links" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-700">
                    Open Payment Links
                </Link>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[420px_minmax(0,1fr)]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-red-600">Approval queue</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950">Work that needs attention</h2>
                <div class="mt-5 grid gap-3">
                    <QueueItem label="Pending dealers" :value="stats.pending_dealers" href="/admin/dealers" />
                    <QueueItem label="Pending dealer cars" :value="stats.pending_cars" href="/admin/cars" />
                    <QueueItem label="Pending customer listings" :value="stats.pending_customer_listings" href="/admin/customer-listings" />
                    <QueueItem label="Unread contact enquiries" :value="stats.contact_enquiries" href="/admin/contact-enquiries" />
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-sky-700">Service analytics</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-950">Paid lookup usage</h2>
                    </div>
                    <Link href="/admin/service-tracking/vehicle-search" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700 sm:w-fit">
                        View Tracking
                    </Link>
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <ServiceMetric label="RC lookups" :value="stats.vahan_lookups" />
                    <ServiceMetric label="Mahindra lookups" :value="stats.mahindra_lookups" />
                    <ServiceMetric label="Maruti lookups" :value="stats.maruti_lookups" />
                    <ServiceMetric label="Challan lookups" :value="stats.challan_lookups" />
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Latest payments</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950">Recent gateway activity</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[760px] w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Party</th>
                                <th class="px-5 py-3">Gateway</th>
                                <th class="px-5 py-3">Type</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Amount</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="payment in recentPayments" :key="payment.id" class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-slate-950">{{ payment.party }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-600">{{ formatLabel(payment.gateway) }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-600">{{ formatLabel(payment.type || 'payment') }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2.5 py-1 text-xs font-black" :class="statusClass(payment.status)">
                                        {{ formatLabel(payment.status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ formatCurrency(payment.amount) }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-500">{{ payment.created_at }}</td>
                            </tr>
                            <tr v-if="!recentPayments.length">
                                <td colspan="6" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No payment activity yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4">
                <QuickAction title="Dealers" text="Review approvals, KYC, plans and wallet adjustments." href="/admin/dealers" />
                <QuickAction title="Cars" text="Moderate dealer and customer inventory." href="/admin/cars" />
                <QuickAction title="Customer Wallets" text="Review customer recharges and admin deductions." href="/admin/customer-wallet-recharges" />
                <QuickAction title="Payment Settings" text="Control Razorpay, PhonePe and minimum recharge values." href="/admin/payment-settings" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Stats = Record<string, number>;
type GatewayRow = { gateway: string; count: number; amount: number; percent?: number };
type GatewayHealth = {
    razorpay_active: boolean;
    phonepe_active: boolean;
    phonepe_environment: string;
    dealer_min_recharge: number;
    customer_min_recharge: number;
};
type PaymentLinks = {
    total: number;
    pending: number;
    paid: number;
    expired: number;
    pending_amount: number;
};
type RecentPayment = {
    id: number;
    gateway: string;
    amount: number;
    status: string;
    type: string;
    party: string;
    created_at: string;
};

const props = defineProps<{
    stats: Stats;
    gatewaySummary: GatewayRow[];
    paymentLinks: PaymentLinks;
    recentPayments: RecentPayment[];
    gatewayHealth: GatewayHealth;
    generatedAt: string;
}>();

const formatNumber = (value: number | string) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;
const formatLabel = (value: string) => String(value || 'Unknown').replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());

const normalizedGateways = computed(() => {
    const rows = props.gatewaySummary.length ? props.gatewaySummary : [
        { gateway: 'Razorpay', count: 0, amount: 0 },
        { gateway: 'PhonePe', count: 0, amount: 0 },
    ];
    const total = rows.reduce((sum, row) => sum + Number(row.amount || 0), 0);
    return rows.map((row) => ({
        ...row,
        percent: total > 0 ? Math.max(4, Math.round((Number(row.amount || 0) / total) * 100)) : 4,
    }));
});

const statusClass = (status: string) => {
    if (status === 'completed' || status === 'paid') return 'bg-teal-50 text-teal-700 ring-1 ring-teal-100';
    if (status === 'failed' || status === 'expired') return 'bg-red-50 text-red-700 ring-1 ring-red-100';
    return 'bg-orange-50 text-orange-700 ring-1 ring-orange-100';
};

const KpiCard = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        helper: { type: String, required: true },
        href: { type: String, required: true },
        tone: { type: String, default: 'teal' },
    },
    setup(cardProps) {
        const toneClass = computed(() => ({
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            orange: 'border-orange-100 bg-orange-50 text-orange-700',
            red: 'border-red-100 bg-red-50 text-red-700',
            sky: 'border-sky-100 bg-sky-50 text-sky-700',
        }[cardProps.tone] || 'border-teal-100 bg-teal-50 text-teal-700'));

        return () => h(Link, {
            href: cardProps.href,
            class: 'group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg',
        }, () => [
            h('div', { class: ['mb-4 inline-flex rounded-lg border px-3 py-2 text-xs font-black uppercase tracking-wide', toneClass.value] }, cardProps.label),
            h('p', { class: 'text-3xl font-black text-slate-950' }, cardProps.value),
            h('p', { class: 'mt-2 text-sm font-semibold leading-6 text-slate-500' }, cardProps.helper),
            h('span', { class: 'mt-4 inline-flex text-sm font-black text-teal-700 group-hover:text-orange-600' }, 'Open'),
        ]);
    },
});

const GatewayStatus = defineComponent({
    props: {
        label: { type: String, required: true },
        active: { type: Boolean, required: true },
        meta: { type: String, default: '' },
    },
    setup(statusProps) {
        return () => h('div', { class: 'rounded-lg border border-white/10 bg-white/5 p-4' }, [
            h('div', { class: 'flex items-center justify-between gap-3' }, [
                h('div', [
                    h('p', { class: 'text-sm font-black text-white' }, statusProps.label),
                    statusProps.meta ? h('p', { class: 'mt-1 text-xs font-bold uppercase tracking-wide text-slate-300' }, statusProps.meta) : null,
                ]),
                h('span', {
                    class: [
                        'rounded-md px-2.5 py-1 text-xs font-black',
                        statusProps.active ? 'bg-teal-400/15 text-teal-200 ring-1 ring-teal-300/20' : 'bg-red-400/15 text-red-200 ring-1 ring-red-300/20',
                    ],
                }, statusProps.active ? 'Active' : 'Off'),
            ]),
        ]);
    },
});

const MiniMetric = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
    },
    setup(metricProps) {
        return () => h('div', [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-slate-400' }, metricProps.label),
            h('p', { class: 'mt-1 text-lg font-black text-white' }, metricProps.value),
        ]);
    },
});

const StatusTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const tileClass = computed(() => ({
            teal: 'bg-teal-50 text-teal-700 ring-teal-100',
            orange: 'bg-orange-50 text-orange-700 ring-orange-100',
            red: 'bg-red-50 text-red-700 ring-red-100',
            slate: 'bg-slate-50 text-slate-700 ring-slate-100',
        }[tileProps.tone] || 'bg-slate-50 text-slate-700 ring-slate-100'));

        return () => h('div', { class: ['rounded-lg p-4 ring-1', tileClass.value] }, [
            h('p', { class: 'text-2xl font-black text-slate-950' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const QueueItem = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
        href: { type: String, required: true },
    },
    setup(itemProps) {
        return () => h(Link, {
            href: itemProps.href,
            class: 'flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 transition hover:border-teal-200 hover:bg-white',
        }, () => [
            h('span', { class: 'text-sm font-black text-slate-700' }, itemProps.label),
            h('span', {
                class: [
                    'rounded-md px-2.5 py-1 text-sm font-black',
                    itemProps.value > 0 ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'bg-teal-50 text-teal-700 ring-1 ring-teal-100',
                ],
            }, formatNumber(itemProps.value)),
        ]);
    },
});

const ServiceMetric = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
    },
    setup(metricProps) {
        return () => h('div', { class: 'rounded-lg border border-slate-200 bg-slate-50 p-4' }, [
            h('p', { class: 'text-2xl font-black text-slate-950' }, formatNumber(metricProps.value)),
            h('p', { class: 'mt-1 text-sm font-bold text-slate-500' }, metricProps.label),
        ]);
    },
});

const QuickAction = defineComponent({
    props: {
        title: { type: String, required: true },
        text: { type: String, required: true },
        href: { type: String, required: true },
    },
    setup(actionProps) {
        return () => h(Link, {
            href: actionProps.href,
            class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg',
        }, () => [
            h('h3', { class: 'text-lg font-black text-slate-950' }, actionProps.title),
            h('p', { class: 'mt-2 text-sm font-semibold leading-6 text-slate-600' }, actionProps.text),
        ]);
    },
});
</script>
