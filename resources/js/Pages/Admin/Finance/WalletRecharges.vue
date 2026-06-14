<template>
    <Head title="Dealer Wallet Recharges" />

    <AdminLayout title="Dealer Wallet Recharges" eyebrow="Dealer finance">
        <FinanceHeader
            title="Trace dealer wallet recharges"
            text="Filter, export and review dealer wallet credits across Razorpay, PhonePe and direct deposits."
            :export-urls="exportUrls"
        />

        <FilterPanel :filters="filters" clear-url="/admin/wallet-recharges" @apply="applyFilters" />

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1060px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Transaction</th>
                            <th class="px-5 py-3">Dealer</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Gateway</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="txn in transactions.data" :key="txn.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ txn.date }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ txn.time }}</p>
                                <span class="mt-2 inline-flex rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-black text-slate-600">{{ txn.receipt }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ txn.dealer.company_name }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-600">{{ txn.dealer.name }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ txn.dealer.unique_id }} <span v-if="txn.dealer.phone">/ {{ txn.dealer.phone }}</span></p>
                                <span class="mt-2 inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-600">
                                    {{ txn.dealer.gst_number ? `GST: ${txn.dealer.gst_number}` : 'Unregistered' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <AmountStack :amount="txn.amount" :gst="txn.gst" :total="txn.total" />
                            </td>
                            <td class="px-5 py-4">
                                <GatewayBlock :transaction="txn" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a :href="txn.receipt_url" class="inline-flex rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-black text-teal-700 transition hover:bg-white">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="5" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No wallet recharges found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try changing the date, gateway or dealer filters.</p>
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
import { defineComponent, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Transaction = {
    id: number;
    date: string;
    time: string;
    receipt: string;
    amount: number;
    gst: number;
    total: number;
    gateway: string;
    reference_id?: string | null;
    secondary_reference?: string | null;
    secondary_reference_label: string;
    reference_type: string;
    dealer: { name: string; company_name: string; phone?: string; unique_id: string; gst_number?: string | null };
    receipt_url: string;
};

defineProps<{
    transactions: { data: Transaction[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: Record<string, string>;
    exportUrls: { excel: string; pdf: string };
}>();

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = (nextFilters: Record<string, string>) => {
    const params = Object.fromEntries(Object.entries(nextFilters).filter(([, value]) => value));
    router.get('/admin/wallet-recharges', params, { preserveState: true, preserveScroll: true });
};

const FinanceHeader = defineComponent({
    props: {
        title: { type: String, required: true },
        text: { type: String, required: true },
        exportUrls: { type: Object as () => { excel: string; pdf: string }, required: true },
    },
    setup(headerProps) {
        return () => h('section', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('div', { class: 'flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between' }, [
                h('div', [
                    h('p', { class: 'text-xs font-black uppercase tracking-wide text-teal-700' }, 'Wallet reports'),
                    h('h2', { class: 'mt-2 text-3xl font-black text-slate-950' }, headerProps.title),
                    h('p', { class: 'mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600' }, headerProps.text),
                ]),
                h('div', { class: 'grid gap-2 sm:grid-cols-2' }, [
                    h('a', { href: headerProps.exportUrls.excel, class: 'rounded-lg bg-teal-700 px-4 py-3 text-center text-sm font-black text-white hover:bg-teal-800' }, 'Export Excel'),
                    h('a', { href: headerProps.exportUrls.pdf, class: 'rounded-lg bg-red-600 px-4 py-3 text-center text-sm font-black text-white hover:bg-red-700' }, 'Export PDF'),
                ]),
            ]),
        ]);
    },
});

const FilterPanel = defineComponent({
    props: {
        filters: { type: Object as () => Record<string, string>, required: true },
        clearUrl: { type: String, required: true },
    },
    emits: ['apply'],
    setup(panelProps, { emit }) {
        const local = { ...panelProps.filters };
        return () => h('section', { class: 'mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('form', {
                class: 'grid gap-3 md:grid-cols-2 xl:grid-cols-[1fr_1fr_1.1fr_1.4fr_auto]',
                onSubmit: (event: Event) => {
                    event.preventDefault();
                    emit('apply', local);
                },
            }, [
                input('From Date', 'from_date', 'date', local),
                input('To Date', 'to_date', 'date', local),
                selectGateway(local),
                input('Search Dealer', 'search', 'text', local, 'ID, name, email, phone'),
                h('div', { class: 'flex items-end gap-2' }, [
                    h('button', { type: 'submit', class: 'h-12 rounded-lg bg-slate-950 px-4 text-sm font-black text-white hover:bg-teal-700' }, 'Filter'),
                    h('a', { href: panelProps.clearUrl, class: 'grid h-12 place-items-center rounded-lg border border-slate-200 px-4 text-sm font-black text-slate-700' }, 'Clear'),
                ]),
            ]),
        ]);
    },
});

const input = (label: string, key: string, type: string, local: Record<string, string>, placeholder = '') => h('label', { class: 'block' }, [
    h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, label),
    h('input', {
        type,
        value: local[key] || '',
        placeholder,
        class: 'h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100',
        onInput: (event: Event) => { local[key] = (event.target as HTMLInputElement).value; },
    }),
]);

const selectGateway = (local: Record<string, string>) => h('label', { class: 'block' }, [
    h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, 'Gateway'),
    h('select', {
        value: local.payment_gateway || '',
        class: 'h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100',
        onChange: (event: Event) => { local.payment_gateway = (event.target as HTMLSelectElement).value; },
    }, [
        h('option', { value: '' }, 'All'),
        h('option', { value: 'razorpay' }, 'Razorpay'),
        h('option', { value: 'phonepe' }, 'PhonePe'),
        h('option', { value: 'direct_deposit' }, 'Direct Deposit'),
    ]),
]);

const AmountStack = defineComponent({
    props: {
        amount: { type: Number, required: true },
        gst: { type: Number, required: true },
        total: { type: Number, required: true },
    },
    setup(amountProps) {
        return () => h('div', { class: 'grid gap-1 text-sm' }, [
            h('p', { class: 'flex justify-between gap-4 font-semibold text-slate-600' }, [h('span', 'Base'), h('span', { class: 'text-teal-700' }, formatCurrency(amountProps.amount))]),
            h('p', { class: 'flex justify-between gap-4 font-semibold text-slate-600' }, [h('span', 'GST 18%'), h('span', { class: 'text-red-600' }, formatCurrency(amountProps.gst))]),
            h('p', { class: 'mt-1 flex justify-between gap-4 border-t border-slate-200 pt-2 font-black text-slate-950' }, [h('span', 'Total'), h('span', formatCurrency(amountProps.total))]),
        ]);
    },
});

const GatewayBlock = defineComponent({
    props: { transaction: { type: Object as () => Transaction, required: true } },
    setup(blockProps) {
        return () => h('div', [
            h('span', { class: 'inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700' }, blockProps.transaction.gateway),
            h('p', { class: 'mt-2 max-w-[260px] break-all text-xs font-semibold text-slate-500' }, `Txn: ${blockProps.transaction.reference_id || 'N/A'}`),
            blockProps.transaction.secondary_reference
                ? h('p', { class: 'mt-1 max-w-[260px] break-all text-xs font-semibold text-slate-500' }, `${blockProps.transaction.secondary_reference_label}: ${blockProps.transaction.secondary_reference}`)
                : null,
        ]);
    },
});
</script>
