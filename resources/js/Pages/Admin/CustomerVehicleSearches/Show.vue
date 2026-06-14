<template>
    <Head :title="`Customer RC Search ${search.registration_number}`" />

    <AdminLayout title="Customer RC Search Details" eyebrow="RC operations">
        <RcSearchTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <Link :href="search.actions.back" class="inline-flex rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-50">Back to searches</Link>
                    <p class="mt-5 text-xs font-black uppercase tracking-wide text-teal-700">Customer registration record</p>
                    <h2 class="mt-2 text-3xl font-black uppercase text-slate-950">{{ search.registration_number }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        {{ search.customer_name || 'Guest customer' }}
                        <span v-if="search.customer_phone"> / {{ search.customer_phone }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <StatusBadge :success="search.is_success" />
                    <a v-if="search.is_success" :href="search.actions.pdf" class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-700">Download PDF</a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <InfoTile label="Amount paid" :value="formatCurrency(search.paid_amount)" />
            <InfoTile label="Payment state" :value="paymentState" tone="teal" />
            <InfoTile label="Refund state" :value="search.is_refunded ? 'Refunded' : 'Not refunded'" tone="blue" />
            <InfoTile label="Search date" :value="search.created_at || 'N/A'" tone="orange" />
        </section>

        <section v-if="search.error_message" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-5 py-4">
            <p class="text-xs font-black uppercase tracking-wide text-red-700">Lookup error</p>
            <p class="mt-2 text-sm font-bold leading-6 text-red-700">{{ search.error_message }}</p>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(320px,0.65fr)]">
            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Vahan response</p>
                    <h3 class="mt-1 text-xl font-black text-slate-950">Vehicle information</h3>
                </div>
                <div v-if="vehicleRows.length" class="overflow-x-auto">
                    <table class="w-full min-w-[620px] text-left text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="row in vehicleRows" :key="row.key">
                                <th class="w-[38%] bg-slate-50 px-5 py-3 text-xs font-black uppercase tracking-wide text-slate-500">{{ prettyLabel(row.key) }}</th>
                                <td class="break-words px-5 py-3 font-bold text-slate-800">{{ stringifyValue(row.value) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="px-5 py-14 text-center">
                    <p class="text-lg font-black text-slate-950">No vehicle response stored</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">This search record does not contain a detailed Vahan payload.</p>
                </div>
            </article>

            <div class="grid content-start gap-5">
                <DetailPanel
                    title="Customer"
                    :rows="[
                        { label: 'Name', value: search.customer_name },
                        { label: 'Phone', value: search.customer_phone },
                        { label: 'Email', value: search.customer_email },
                    ]"
                />
                <DetailPanel
                    title="Payment references"
                    :rows="[
                        { label: 'Order ID', value: search.razorpay_order_id },
                        { label: 'Payment ID', value: search.razorpay_payment_id },
                        { label: 'Refund ID', value: search.razorpay_refund_id },
                    ]"
                />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RcSearchTabs from '@/Components/Admin/RcSearchTabs.vue';

type SearchDetail = {
    registration_number: string;
    customer_name?: string | null;
    customer_phone?: string | null;
    customer_email?: string | null;
    paid_amount: number;
    is_success: boolean;
    is_refunded: boolean;
    error_message?: string | null;
    created_at?: string | null;
    razorpay_order_id?: string | null;
    razorpay_payment_id?: string | null;
    razorpay_refund_id?: string | null;
    vehicle_data: Record<string, unknown>;
    actions: { back: string; pdf: string };
};

const props = defineProps<{ search: SearchDetail }>();
const vehicleRows = computed(() => Object.entries(props.search.vehicle_data || {}).map(([key, value]) => ({ key, value })));
const paymentState = computed(() => props.search.razorpay_payment_id ? 'Payment captured' : (props.search.is_success ? 'Recorded without payment ID' : 'No completed result'));
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const prettyLabel = (key: string) => key.replace(/[_-]+/g, ' ').replace(/\w\S*/g, (word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase());
const stringifyValue = (value: unknown) => {
    if (value === null || value === undefined || value === '') return 'N/A';
    return typeof value === 'object' ? JSON.stringify(value) : String(value);
};

const InfoTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const colors: Record<string, string> = {
            slate: 'border-slate-200 bg-white',
            teal: 'border-teal-100 bg-teal-50',
            blue: 'border-blue-100 bg-blue-50',
            orange: 'border-orange-100 bg-orange-50',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-slate-500' }, tileProps.label),
            h('p', { class: 'mt-2 text-lg font-black text-slate-950' }, tileProps.value),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-3 py-2 text-xs font-black',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.success ? 'Successful lookup' : 'Failed lookup');
    },
});

const DetailPanel = defineComponent({
    props: {
        title: { type: String, required: true },
        rows: { type: Array as () => Array<{ label: string; value: unknown }>, required: true },
    },
    setup(panelProps) {
        return () => h('article', { class: 'overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm' }, [
            h('div', { class: 'border-b border-slate-100 px-5 py-4' }, [
                h('h3', { class: 'text-lg font-black text-slate-950' }, panelProps.title),
            ]),
            h('dl', { class: 'divide-y divide-slate-100' }, panelProps.rows.map((row) => h('div', { class: 'px-5 py-3' }, [
                h('dt', { class: 'text-xs font-black uppercase tracking-wide text-slate-500' }, row.label),
                h('dd', { class: 'mt-1 break-all text-sm font-bold text-slate-800' }, stringifyValue(row.value)),
            ]))),
        ]);
    },
});
</script>
