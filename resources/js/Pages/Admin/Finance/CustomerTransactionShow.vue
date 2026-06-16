<template>
    <Head title="Customer Transaction Details" />

    <AdminLayout title="Transaction Details" eyebrow="Customer finance">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <Link
                        :href="`/admin/customer-transactions?type=${type}`"
                        class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Back to list
                    </Link>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-teal-700">{{ currentTabLabel }}</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">{{ transaction.vehicle_number || 'Vehicle request' }}</h2>
                    <p class="mt-2 text-sm font-semibold leading-7 text-slate-600">
                        {{ transaction.customer_name || 'N/A' }}
                        <span v-if="transaction.customer_phone"> / {{ transaction.customer_phone }}</span>
                        <span v-if="transaction.customer_email"> / {{ transaction.customer_email }}</span>
                    </p>
                    <p class="mt-1 text-xs font-bold text-slate-500">{{ transaction.date }}</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[420px]">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Paid amount</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950">{{ formatCurrency(transaction.paid_amount) }}</p>
                        <StatusBadge class="mt-3" :success="transaction.is_success" />
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Refund state</p>
                        <RefundBadge class="mt-3" :transaction="transaction" />
                        <button
                            v-if="transaction.can_refund"
                            type="button"
                            class="mt-4 w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                            @click="refund"
                        >
                            Issue Refund
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-3">
            <InfoCard label="Order ID" :value="transaction.razorpay_order_id || 'N/A'" />
            <InfoCard label="Payment ID" :value="transaction.razorpay_payment_id || 'N/A'" mono />
            <InfoCard label="Refund ID" :value="transaction.razorpay_refund_id || 'N/A'" mono />
        </section>

        <section v-if="transaction.error_message" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
            {{ transaction.error_message }}
        </section>

        <section v-if="type === 'challan'" class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <SectionHeader
                title="Challan result"
                :subtitle="`${transaction.challan_count || 0} challans / ${formatCurrency(transaction.total_amount || 0)} fine amount`"
            />
            <div v-if="challans.length" class="overflow-x-auto">
                <table class="min-w-[940px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Challan No</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Location</th>
                            <th class="px-5 py-3">Offence</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(challan, index) in challans" :key="`${challan.challanNo || 'challan'}-${index}`" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ challan.challanNo || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ formatDate(challan.dateChallan) }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ challan.locationChallan || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ challan.detailsViolation?.[0]?.offence || 'N/A' }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ formatCurrency(challan.amountChallan || 0) }}</td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold"
                                    :class="challan.status === 'Paid' ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'"
                                >
                                    {{ challan.status || 'Unknown' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No pending challans found" text="The request completed successfully and returned no challan records." />
        </section>

        <section v-else-if="type === 'vahan'" class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <SectionHeader title="Vehicle result" subtitle="Raw Vahan profile data returned for this paid lookup." />
            <div v-if="vehicleRows.length" class="overflow-x-auto">
                <table class="min-w-[720px] w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="row in vehicleRows" :key="row.key" class="hover:bg-slate-50">
                            <td class="w-1/3 bg-slate-50 px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ prettyLabel(row.key) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ stringifyValue(row.value) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No vehicle data available" text="The payment record is present, but no detailed Vahan response was stored." />
        </section>

        <section v-else-if="type === 'maruti'" class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <SectionHeader title="Maruti service records" subtitle="Workshop history attached to this customer request." />
            <div v-if="records.length" class="overflow-x-auto">
                <table class="min-w-[920px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Service Type</th>
                            <th class="px-5 py-3">Dealer</th>
                            <th class="px-5 py-3">Job Card / RO No</th>
                            <th class="px-5 py-3">Mileage</th>
                            <th class="px-5 py-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(record, index) in records" :key="index" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ record.svc_date || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.service_cate || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.dealer_name || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.register_no || 'N/A' }} / {{ record.repair_order_no || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.mileage || 'N/A' }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ formatCurrency(record.total_amount || 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No service records found" text="No Maruti service rows were attached to this transaction." />
        </section>

        <section v-else class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <SectionHeader title="Mahindra service records" subtitle="Service history rows attached to this customer request." />
            <div v-if="records.length" class="overflow-x-auto">
                <table class="min-w-[820px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Dealer</th>
                            <th class="px-5 py-3">Work Type</th>
                            <th class="px-5 py-3">Mileage</th>
                            <th class="px-5 py-3">Bill Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="(record, index) in records" :key="index" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ record.svc_date || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.dealer_name || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.work_type || 'N/A' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ record.mileage ? `${record.mileage} km` : 'N/A' }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ formatCurrency(record.net_bill_amt || 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No service records found" text="No Mahindra service rows were attached to this transaction." />
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Tab = { label: string; value: string };

type Challan = {
    challanNo?: string | null;
    dateChallan?: string | null;
    locationChallan?: string | null;
    detailsViolation?: Array<{ offence?: string | null }>;
    amountChallan?: number | string | null;
    status?: string | null;
};

type ServiceRecord = {
    svc_date?: string | null;
    service_cate?: string | null;
    dealer_name?: string | null;
    register_no?: string | null;
    repair_order_no?: string | null;
    mileage?: string | number | null;
    total_amount?: number;
    work_type?: string | null;
    net_bill_amt?: number;
};

type Transaction = {
    id: number;
    customer_name?: string | null;
    customer_phone?: string | null;
    customer_email?: string | null;
    vehicle_number?: string | null;
    paid_amount: number;
    is_success: boolean;
    is_refunded: boolean;
    razorpay_order_id?: string | null;
    razorpay_payment_id?: string | null;
    razorpay_refund_id?: string | null;
    date?: string | null;
    error_message?: string | null;
    challan_count?: number;
    total_amount?: number;
    vehicle_data?: Record<string, unknown> | null;
    challan_data?: Challan[] | null;
    records?: ServiceRecord[];
    refund_url: string;
    can_refund: boolean;
};

const props = defineProps<{
    transaction: Transaction;
    type: string;
    tabs: Tab[];
}>();

const currentTabLabel = computed(() => props.tabs.find((tab) => tab.value === props.type)?.label || 'Customer service');
const challans = computed(() => props.transaction.challan_data || []);
const records = computed(() => props.transaction.records || []);
const vehicleRows = computed(() => Object.entries(props.transaction.vehicle_data || {}).map(([key, value]) => ({ key, value })));

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const prettyLabel = (key: string) => key
    .replace(/[_-]+/g, ' ')
    .replace(/\w\S*/g, (word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase());

const stringifyValue = (value: unknown) => {
    if (value === null || value === undefined || value === '') {
        return 'N/A';
    }
    if (typeof value === 'object') {
        return JSON.stringify(value);
    }
    return String(value);
};

const formatDate = (value?: string | null) => {
    if (!value) {
        return 'N/A';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }).format(date);
};

const refund = () => {
    if (!window.confirm('Issue a full refund to this customer?')) {
        return;
    }

    router.post(props.transaction.refund_url, {}, { preserveScroll: true });
};

const InfoCard = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        mono: { type: Boolean, default: false },
    },
    setup(cardProps) {
        return () => h('div', { class: 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm' }, [
            h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-slate-500' }, cardProps.label),
            h('p', {
                class: [
                    'mt-2 break-all text-sm font-semibold text-slate-950',
                    cardProps.mono ? 'font-mono' : '',
                ],
            }, cardProps.value),
        ]);
    },
});

const SectionHeader = defineComponent({
    props: {
        title: { type: String, required: true },
        subtitle: { type: String, required: true },
    },
    setup(sectionProps) {
        return () => h('div', { class: 'border-b border-slate-100 px-5 py-4' }, [
            h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-teal-700' }, 'Response data'),
            h('h3', { class: 'mt-1 text-xl font-semibold text-slate-950' }, sectionProps.title),
            h('p', { class: 'mt-1 text-sm font-semibold text-slate-500' }, sectionProps.subtitle),
        ]);
    },
});

const EmptyState = defineComponent({
    props: {
        title: { type: String, required: true },
        text: { type: String, required: true },
    },
    setup(emptyProps) {
        return () => h('div', { class: 'px-5 py-14 text-center' }, [
            h('p', { class: 'text-lg font-semibold text-slate-950' }, emptyProps.title),
            h('p', { class: 'mt-2 text-sm font-semibold text-slate-500' }, emptyProps.text),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: {
        success: { type: Boolean, required: true },
        class: { type: String, default: '' },
    },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-semibold',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
                badgeProps.class,
            ],
        }, badgeProps.success ? 'Success' : 'Failed API');
    },
});

const RefundBadge = defineComponent({
    props: {
        transaction: { type: Object as () => Transaction, required: true },
        class: { type: String, default: '' },
    },
    setup(badgeProps) {
        return () => {
            if (!badgeProps.transaction.razorpay_payment_id) {
                return h('span', { class: ['inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600', badgeProps.class] }, 'Unpaid / No ID');
            }

            if (badgeProps.transaction.is_refunded) {
                return h('div', { class: badgeProps.class }, [
                    h('span', { class: 'inline-flex rounded-md bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-100' }, 'Refunded'),
                    h('p', { class: 'mt-1 break-all text-xs font-semibold text-slate-500' }, badgeProps.transaction.razorpay_refund_id || 'Refund ID pending'),
                ]);
            }

            return h('span', { class: ['inline-flex rounded-md bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-100', badgeProps.class] }, 'Not Refunded');
        };
    },
});
</script>
