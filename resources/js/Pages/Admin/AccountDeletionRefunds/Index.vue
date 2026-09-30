<template>
    <Head title="Deletion Refunds" />

    <AdminLayout title="Deletion Refunds" eyebrow="Revenue">
        <p class="max-w-3xl text-sm font-medium text-slate-600">
            Wallet balances left behind when a customer deleted their account. Deleting is never blocked by a balance,
            because a customer cannot withdraw one themselves — so the money is recorded here and returned by you.
            Pay the customer first, then record it below.
        </p>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Tile label="Awaiting refund" :value="formatNumber(totals.pendingCount)" :tone="totals.pendingCount ? 'red' : 'slate'" />
            <Tile label="Amount owed" :value="money(totals.pendingTotal)" :tone="totals.pendingTotal ? 'orange' : 'slate'" />
            <Tile label="Refunded" :value="formatNumber(totals.refundedCount)" tone="teal" />
            <Tile label="Refunded value" :value="money(totals.refundedTotal)" tone="teal" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="option in statuses"
                    :key="option.value"
                    type="button"
                    class="rounded-lg border px-4 py-2 text-sm font-semibold transition"
                    :class="filters.status === option.value
                        ? 'border-teal-200 bg-teal-50 text-teal-700'
                        : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                    @click="setStatus(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Pay to</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Deleted on</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="refund in refunds.data" :key="refund.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ refund.contact_name || 'Deleted user' }}</p>
                                <p class="mt-0.5 text-xs font-medium text-slate-500">Customer #{{ refund.customer_id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p v-if="refund.contact_phone" class="font-semibold text-slate-950">{{ refund.contact_phone }}</p>
                                <p v-else class="text-xs font-medium text-slate-400">Removed after refund</p>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-slate-950">{{ money(refund.balance) }}</td>
                            <td class="px-5 py-4 font-medium text-slate-600">{{ refund.requested_at }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md px-2 py-0.5 text-xs font-semibold" :class="toneFor(refund.status)">
                                    {{ labelFor(refund.status) }}
                                </span>
                                <p v-if="refund.refunded_at" class="mt-1 text-xs font-medium text-slate-500">
                                    {{ refund.refunded_at }}<span v-if="refund.reference"> &middot; {{ refund.reference }}</span>
                                </p>
                                <p v-else-if="refund.notes" class="mt-1 max-w-[220px] text-xs font-medium text-slate-500">{{ refund.notes }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    v-if="refund.is_pending"
                                    type="button"
                                    class="rounded-lg border border-teal-200 px-3 py-2 text-xs font-semibold text-teal-700 hover:bg-teal-50"
                                    @click="markRefunded(refund)"
                                >
                                    Mark refunded
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!refunds.data.length">
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-base font-bold text-slate-950">Nothing here</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">
                                    A row appears when someone deletes their account with money still in their wallet.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="refunds.data.length" class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="refunds.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

const props = defineProps<{
    refunds: { data: Array<any>; links: Array<any> };
    filters: { status: string };
    totals: { pendingCount: number; pendingTotal: number; refundedCount: number; refundedTotal: number };
}>();

const statuses = [
    { value: 'pending', label: 'Awaiting refund' },
    { value: 'refunded', label: 'Refunded' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'all', label: 'All' },
];

const setStatus = (status: string) => {
    router.get('/admin/account-deletion-refunds', status === 'all' ? {} : { status }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const markRefunded = (refund: any) => {
    // This screen records a refund, it does not make one. The money moves in
    // the payment gateway or the bank; confirming here closes the record and
    // drops the phone number, which was only kept in order to pay them.
    const reference = window.prompt(
        `Record a refund of ${money(refund.balance)} to ${refund.contact_phone}?\n\n`
            + 'Only do this once the money has actually been sent. The stored phone number '
            + 'and name are deleted when you confirm.\n\n'
            + 'Reference (UTR, payment id) — optional:',
    );

    if (reference === null) return;

    router.post(refund.markRefunded, { reference: reference.trim() || null }, { preserveScroll: true });
};

const labelFor = (status: string) =>
    ({ pending: 'Awaiting refund', refunded: 'Refunded', cancelled: 'Cancelled' } as Record<string, string>)[status] || status;

const toneFor = (status: string) =>
    ({
        pending: 'bg-red-50 text-red-700',
        refunded: 'bg-teal-50 text-teal-700',
        cancelled: 'bg-slate-100 text-slate-600',
    } as Record<string, string>)[status] || 'bg-slate-100 text-slate-600';

const formatNumber = (value: number) => new Intl.NumberFormat('en-IN').format(Number(value || 0));
const money = (value: number) =>
    `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const Tile = defineComponent({
    props: { label: { type: String, required: true }, value: { type: String, required: true }, tone: { type: String, default: 'slate' } },
    setup(p) {
        const tones: Record<string, string> = {
            slate: 'border-slate-200 bg-white text-slate-950',
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            orange: 'border-orange-100 bg-orange-50 text-orange-700',
            red: 'border-red-100 bg-red-50 text-red-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', tones[p.tone] || tones.slate] }, [
            h('p', { class: 'text-xl font-bold tracking-tight' }, p.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, p.label),
        ]);
    },
});
</script>
