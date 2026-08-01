<template>
    <Head title="Invoice Register" />

    <AdminLayout title="Invoice Register" eyebrow="GST compliance">
        <div class="flex flex-wrap items-center gap-2">
            <Link :href="actions.settings" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                Invoice settings
            </Link>
            <a :href="actions.exportExcel" class="rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
                Export for GSTR-1 (Excel)
            </a>
        </div>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <Tile label="Invoices" :value="formatNumber(totals.count)" />
            <Tile label="Taxable value" :value="money(totals.taxable)" />
            <Tile label="CGST + SGST" :value="money(totals.cgst + totals.sgst)" tone="teal" />
            <Tile label="IGST" :value="money(totals.igst)" tone="blue" />
            <Tile label="Invoice total" :value="money(totals.total)" tone="orange" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6 xl:items-end">
                <label class="xl:col-span-2">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Search</span>
                    <input v-model="form.search" class="admin-input" type="search" placeholder="Invoice no, buyer or GSTIN" @keyup.enter="apply" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Financial year</span>
                    <select v-model="form.financial_year" class="admin-input">
                        <option value="">All years</option>
                        <option v-for="fy in financialYears" :key="fy" :value="fy">{{ fy }}</option>
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Party</span>
                    <select v-model="form.party_type" class="admin-input">
                        <option value="">All</option>
                        <option value="dealer">Dealers</option>
                        <option value="customer">Customers</option>
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Supply</span>
                    <select v-model="form.supply_type" class="admin-input">
                        <option value="">All</option>
                        <option value="intra">Intra-state</option>
                        <option value="inter">Inter-state</option>
                    </select>
                </label>
                <div class="flex gap-2">
                    <button type="button" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800" @click="apply">Filter</button>
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="clear">Clear</button>
                </div>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">From date</span>
                    <input v-model="form.from_date" class="admin-input" type="date" />
                </label>
                <label>
                    <span class="mb-2 block text-sm font-semibold text-slate-700">To date</span>
                    <input v-model="form.to_date" class="admin-input" type="date" />
                </label>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1040px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Buyer</th>
                            <th class="px-5 py-3">Place of supply</th>
                            <th class="px-5 py-3 text-right">Taxable</th>
                            <th class="px-5 py-3 text-right">CGST</th>
                            <th class="px-5 py-3 text-right">SGST</th>
                            <th class="px-5 py-3 text-right">IGST</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ invoice.invoice_number }}</p>
                                <span class="mt-1 inline-flex rounded-md px-2 py-0.5 text-xs font-semibold" :class="invoice.is_intra_state ? 'bg-teal-50 text-teal-700' : 'bg-blue-50 text-blue-700'">
                                    {{ invoice.is_intra_state ? 'Intra' : 'Inter' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-600">{{ invoice.issued_at }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ invoice.buyer }}</p>
                                <p class="mt-0.5 text-xs font-medium text-slate-500">{{ invoice.party_type }} &middot; {{ invoice.buyer_gstin || 'Unregistered' }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-600">{{ invoice.place_of_supply || '-' }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ money(invoice.taxable_value) }}</td>
                            <td class="px-5 py-4 text-right font-medium text-slate-600">{{ invoice.cgst ? money(invoice.cgst) : '-' }}</td>
                            <td class="px-5 py-4 text-right font-medium text-slate-600">{{ invoice.sgst ? money(invoice.sgst) : '-' }}</td>
                            <td class="px-5 py-4 text-right font-medium text-slate-600">{{ invoice.igst ? money(invoice.igst) : '-' }}</td>
                            <td class="px-5 py-4 text-right font-bold text-slate-950">{{ money(invoice.total_amount) }}</td>
                            <td class="px-5 py-4 text-right">
                                <a :href="invoice.download" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">PDF</a>
                            </td>
                        </tr>
                        <tr v-if="!invoices.data.length">
                            <td colspan="10" class="px-5 py-12 text-center">
                                <p class="text-base font-bold text-slate-950">No invoices yet</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">Tax invoices are issued automatically when a dealer or customer recharges their wallet.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="invoices.data.length" class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="invoices.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

const props = defineProps<{
    invoices: { data: Array<any>; links: Array<any> };
    filters: Record<string, string>;
    financialYears: string[];
    totals: { count: number; taxable: number; cgst: number; sgst: number; igst: number; total: number };
    actions: { settings: string; exportExcel: string };
}>();

const form = reactive({ ...props.filters });

const apply = () => {
    const params: Record<string, string> = {};
    Object.entries(form).forEach(([key, value]) => { if (value) params[key] = String(value); });
    router.get('/admin/invoices', params, { preserveState: true, preserveScroll: true });
};

const clear = () => {
    Object.keys(form).forEach((key) => { (form as any)[key] = ''; });
    router.get('/admin/invoices', {}, { preserveState: true, preserveScroll: true });
};

const formatNumber = (value: number) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const Tile = defineComponent({
    props: { label: { type: String, required: true }, value: { type: String, required: true }, tone: { type: String, default: 'slate' } },
    setup(p) {
        const tones: Record<string, string> = {
            slate: 'border-slate-200 bg-white text-slate-950',
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            blue: 'border-blue-100 bg-blue-50 text-blue-700',
            orange: 'border-orange-100 bg-orange-50 text-orange-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', tones[p.tone] || tones.slate] }, [
            h('p', { class: 'text-xl font-bold tracking-tight' }, p.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, p.label),
        ]);
    },
});
</script>
