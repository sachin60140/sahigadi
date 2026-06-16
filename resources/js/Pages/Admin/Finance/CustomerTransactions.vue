<template>
    <Head title="Customer Payments & Refunds" />

    <AdminLayout title="Customer Payments & Refunds" eyebrow="Customer finance">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Payment analysis</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Monitor paid customer service requests.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Track RC checks, challan lookups and service-history payments with refund control in one admin view.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[520px]">
                    <MetricTile label="Successful" :value="summary.successful" tone="teal" />
                    <MetricTile label="Failed API" :value="summary.failed" tone="red" />
                    <MetricTile label="Refundable" :value="summary.refundable" tone="orange" />
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
            <div class="flex gap-2 overflow-x-auto">
                <Link
                    v-for="tab in tabs"
                    :key="tab.value"
                    :href="`/admin/customer-transactions?type=${tab.value}`"
                    class="whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold transition"
                    :class="tab.value === type ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'"
                >
                    {{ tab.label }}
                </Link>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">{{ currentTabLabel }}</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-950">Recent transactions</h3>
                    </div>
                    <span class="inline-flex w-fit rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                        {{ transactions.data.length }} visible records
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1040px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Request</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">{{ vehicleColumnLabel }}</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Refund</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">#{{ transaction.id }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ transaction.created_at }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ transaction.customer_name || 'N/A' }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ transaction.customer_phone || 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-800">
                                    {{ transaction.vehicle_number || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-base font-semibold text-slate-950">{{ formatCurrency(transaction.paid_amount) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <StatusBadge :success="transaction.is_success" />
                            </td>
                            <td class="px-5 py-4">
                                <RefundBadge :transaction="transaction" />
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="transaction.show_url"
                                        class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-semibold text-teal-700 transition hover:bg-white"
                                    >
                                        Details
                                    </Link>
                                    <button
                                        v-if="transaction.can_refund"
                                        type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-700 transition hover:bg-white"
                                        @click="refund(transaction)"
                                    >
                                        Refund
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No customer transactions found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Choose another service tab to inspect its payment history.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="transactions.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Tab = { label: string; value: string };

type Transaction = {
    id: number;
    customer_name?: string | null;
    customer_phone?: string | null;
    vehicle_number?: string | null;
    paid_amount: number;
    is_success: boolean;
    is_refunded: boolean;
    razorpay_payment_id?: string | null;
    razorpay_refund_id?: string | null;
    created_at?: string | null;
    show_url: string;
    refund_url: string;
    can_refund: boolean;
};

const props = defineProps<{
    transactions: { data: Transaction[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    type: string;
    tabs: Tab[];
}>();

const currentTabLabel = computed(() => props.tabs.find((tab) => tab.value === props.type)?.label || 'Customer service');
const vehicleColumnLabel = computed(() => (props.type === 'vahan' ? 'Registration No' : 'Vehicle Number'));
const summary = computed(() => ({
    successful: props.transactions.data.filter((transaction) => transaction.is_success).length,
    failed: props.transactions.data.filter((transaction) => !transaction.is_success).length,
    refundable: props.transactions.data.filter((transaction) => transaction.can_refund).length,
}));

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const refund = (transaction: Transaction) => {
    if (!window.confirm('Issue a full refund to this customer?')) {
        return;
    }

    router.post(transaction.refund_url, {}, { preserveScroll: true });
};

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
        tone: { type: String, required: true },
    },
    setup(tileProps) {
        const toneClass = computed(() => {
            if (tileProps.tone === 'red') {
                return 'border-red-100 bg-red-50 text-red-700';
            }
            if (tileProps.tone === 'orange') {
                return 'border-orange-100 bg-orange-50 text-orange-700';
            }
            return 'border-teal-100 bg-teal-50 text-teal-700';
        });

        return () => h('div', { class: ['rounded-lg border p-4', toneClass.value] }, [
            h('p', { class: 'text-2xl font-semibold' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-semibold',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.success ? 'Success' : 'Failed API');
    },
});

const RefundBadge = defineComponent({
    props: { transaction: { type: Object as () => Transaction, required: true } },
    setup(badgeProps) {
        return () => {
            if (!badgeProps.transaction.razorpay_payment_id) {
                return h('span', { class: 'inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600' }, 'Unpaid / No ID');
            }

            if (badgeProps.transaction.is_refunded) {
                return h('div', [
                    h('span', { class: 'inline-flex rounded-md bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-100' }, 'Refunded'),
                    h('p', { class: 'mt-1 max-w-[220px] break-all text-xs font-semibold text-slate-500' }, badgeProps.transaction.razorpay_refund_id || 'Refund ID pending'),
                ]);
            }

            return h('span', { class: 'inline-flex rounded-md bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-100' }, 'Not Refunded');
        };
    },
});
</script>
