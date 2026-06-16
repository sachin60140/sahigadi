<template>
    <Head title="Customer Wallet Recharges" />

    <AdminLayout title="Customer Wallet Recharges" eyebrow="Customer finance">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Wallet reports</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Trace customer wallet movement.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Review customer credits, direct deposits, gateway payments and admin deductions.
                    </p>
                </div>
                <div class="grid gap-2 sm:grid-cols-3">
                    <button type="button" class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600" @click="showDeduct = true">
                        Deduct Balance
                    </button>
                    <a :href="exportUrls.excel" class="rounded-lg bg-teal-700 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-teal-800">Export Excel</a>
                    <a :href="exportUrls.pdf" class="rounded-lg bg-red-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-red-700">Export PDF</a>
                </div>
            </div>
        </section>

        <FilterPanel :filters="filters" @apply="applyFilters" />

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1060px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Transaction</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Gateway</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="txn in transactions.data" :key="txn.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ txn.date }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ txn.time }}</p>
                                <span class="mt-2 inline-flex rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">{{ txn.receipt }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ txn.customer.name }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ txn.customer.unique_id }} <span v-if="txn.customer.phone">/ {{ txn.customer.phone }}</span></p>
                                <span class="mt-2 inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    {{ txn.customer.gst_number ? `GST: ${txn.customer.gst_number}` : 'Unregistered' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div v-if="txn.type === 'debit'" class="font-semibold text-red-600">-{{ formatCurrency(txn.amount) }}</div>
                                <AmountStack v-else :amount="txn.amount" :gst="txn.gst" :total="txn.total" />
                            </td>
                            <td class="px-5 py-4">
                                <GatewayBlock :transaction="txn" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a v-if="txn.receipt_url" :href="txn.receipt_url" class="inline-flex rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-semibold text-teal-700 transition hover:bg-white">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="5" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No customer wallet records found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try changing the filters.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="transactions.links" />
            </div>
        </section>

        <div v-if="showDeduct" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4">
            <button class="absolute inset-0" type="button" aria-label="Close modal" @click="showDeduct = false"></button>
            <form class="relative w-full max-w-xl rounded-lg bg-white p-5 shadow-2xl sm:p-6" @submit.prevent="deduct">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Admin deduction</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-950">Deduct customer balance</h2>
                    </div>
                    <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600" @click="showDeduct = false">Close</button>
                </div>

                <div class="mt-5 grid gap-4">
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Customer</span>
                        <select v-model="deductForm.customer_id" class="admin-input" required>
                            <option value="">Choose customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.label }}</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Amount</span>
                        <input v-model="deductForm.amount" class="admin-input" min="1" step="0.01" required type="number" />
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Remark / Reason</span>
                        <input v-model="deductForm.remark" class="admin-input" required type="text" />
                    </label>
                </div>

                <div v-if="firstError" class="mt-4 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ firstError }}</div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700" @click="showDeduct = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-600" :disabled="deductForm.processing">
                        {{ deductForm.processing ? 'Deducting...' : 'Confirm Deduction' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Transaction = {
    id: number;
    date: string;
    time: string;
    receipt: string;
    type: string;
    amount: number;
    gst: number;
    total: number;
    gateway: string;
    reference_id?: string | null;
    secondary_reference?: string | null;
    secondary_reference_label: string;
    remark?: string | null;
    customer: { name: string; company_name: string; phone?: string; unique_id: string; gst_number?: string | null };
    receipt_url?: string | null;
};

const props = defineProps<{
    transactions: { data: Transaction[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    customers: Array<{ id: number; label: string; balance: number }>;
    filters: Record<string, string>;
    exportUrls: { excel: string; pdf: string };
}>();

const showDeduct = ref(false);
const deductForm = useForm({ customer_id: '', amount: '', remark: '' });
const firstError = computed(() => Object.values(deductForm.errors)[0] || '');

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = (nextFilters: Record<string, string>) => {
    const params = Object.fromEntries(Object.entries(nextFilters).filter(([, value]) => value));
    router.get('/admin/customer-wallet-recharges', params, { preserveState: true, preserveScroll: true });
};

const deduct = () => {
    deductForm.post('/admin/customer-wallet-recharges/deduct', {
        preserveScroll: true,
        onSuccess: () => {
            deductForm.reset();
            showDeduct.value = false;
        },
    });
};

const FilterPanel = defineComponent({
    props: { filters: { type: Object as () => Record<string, string>, required: true } },
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
                input('Search Customer', 'search', 'text', local, 'ID, name, email, phone'),
                h('div', { class: 'flex items-end gap-2' }, [
                    h('button', { type: 'submit', class: 'h-12 rounded-lg bg-slate-950 px-4 text-sm font-semibold text-white hover:bg-teal-700' }, 'Filter'),
                    h('a', { href: '/admin/customer-wallet-recharges', class: 'grid h-12 place-items-center rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700' }, 'Clear'),
                ]),
            ]),
        ]);
    },
});

const input = (label: string, key: string, type: string, local: Record<string, string>, placeholder = '') => h('label', { class: 'block' }, [
    h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, label),
    h('input', {
        type,
        value: local[key] || '',
        placeholder,
        class: 'h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100',
        onInput: (event: Event) => { local[key] = (event.target as HTMLInputElement).value; },
    }),
]);

const selectGateway = (local: Record<string, string>) => h('label', { class: 'block' }, [
    h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, 'Gateway'),
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
    props: { amount: { type: Number, required: true }, gst: { type: Number, required: true }, total: { type: Number, required: true } },
    setup(amountProps) {
        return () => h('div', { class: 'grid gap-1 text-sm' }, [
            h('p', { class: 'flex justify-between gap-4 font-semibold text-slate-600' }, [h('span', 'Base'), h('span', { class: 'text-teal-700' }, formatCurrency(amountProps.amount))]),
            h('p', { class: 'flex justify-between gap-4 font-semibold text-slate-600' }, [h('span', 'GST 18%'), h('span', { class: 'text-red-600' }, formatCurrency(amountProps.gst))]),
            h('p', { class: 'mt-1 flex justify-between gap-4 border-t border-slate-200 pt-2 font-semibold text-slate-950' }, [h('span', 'Total'), h('span', formatCurrency(amountProps.total))]),
        ]);
    },
});

const GatewayBlock = defineComponent({
    props: { transaction: { type: Object as () => Transaction, required: true } },
    setup(blockProps) {
        return () => h('div', [
            h('span', { class: 'inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700' }, blockProps.transaction.gateway),
            h('p', { class: 'mt-2 max-w-[260px] break-all text-xs font-semibold text-slate-500' }, blockProps.transaction.type === 'debit' ? `Remark: ${blockProps.transaction.remark || 'N/A'}` : `Txn: ${blockProps.transaction.reference_id || 'N/A'}`),
            blockProps.transaction.secondary_reference
                ? h('p', { class: 'mt-1 max-w-[260px] break-all text-xs font-semibold text-slate-500' }, `${blockProps.transaction.secondary_reference_label}: ${blockProps.transaction.secondary_reference}`)
                : null,
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
