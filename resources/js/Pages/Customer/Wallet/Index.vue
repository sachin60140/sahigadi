<template>
    <Head title="My Wallet" />
    <CustomerLayout title="My Wallet" eyebrow="Balance and transactions">
        <section class="grid gap-5 xl:grid-cols-[0.8fr_1.2fr]">
            <div class="rounded-lg bg-slate-950 p-5 text-white shadow-sm sm:p-6">
                <p class="text-xs font-black uppercase tracking-wide text-teal-300">Available balance</p>
                <p class="mt-3 text-4xl font-black">{{ money(balance) }}</p>
                <button type="button" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-3 text-sm font-black text-white hover:bg-orange-600" @click="showRecharge = true">
                    <Plus class="h-4 w-4" />
                    Add money
                </button>
            </div>
            <div class="rounded-lg border border-teal-100 bg-teal-50 p-5 sm:p-6">
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Wallet usage</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Pay for verification and listing upgrades.</h2>
                <p class="mt-2 text-sm font-semibold leading-7 text-slate-600">Recharge totals include 18% GST. Successful gateway credits include downloadable receipts.</p>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Ledger</p>
                <h2 class="mt-1 text-xl font-black text-slate-950">Transaction history</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Type</th><th class="px-5 py-3">Amount</th><th class="px-5 py-3">Remark</th><th class="px-5 py-3">Reference</th><th class="px-5 py-3 text-right">Receipt</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in transactions.data" :key="item.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ item.created_at }}</td>
                            <td class="px-5 py-4"><span class="rounded-md px-2.5 py-1 text-xs font-black capitalize" :class="item.type === 'credit' ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'">{{ item.type }}</span></td>
                            <td class="px-5 py-4 font-black" :class="item.type === 'credit' ? 'text-teal-700' : 'text-red-700'">{{ item.type === 'credit' ? '+' : '-' }}{{ money(item.amount) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ item.remark || '-' }}</td>
                            <td class="px-5 py-4 text-xs font-bold text-slate-500">{{ item.reference_id || '-' }}</td>
                            <td class="px-5 py-4 text-right"><a v-if="item.receipt_url" :href="item.receipt_url" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50"><Download class="h-3.5 w-3.5" /> Receipt</a><span v-else class="text-slate-300">-</span></td>
                        </tr>
                        <tr v-if="!transactions.data.length"><td colspan="6" class="px-5 py-14 text-center"><WalletCards class="mx-auto h-10 w-10 text-slate-300" /><p class="mt-3 text-lg font-black text-slate-950">No transactions yet</p></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="transactions.links" /></div>
        </section>

        <div v-if="showRecharge" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4">
            <div class="w-full max-w-lg rounded-lg bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div><p class="text-xs font-black uppercase tracking-wide text-teal-700">Wallet funding</p><h2 class="mt-1 text-xl font-black text-slate-950">Recharge wallet</h2></div>
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-slate-600" aria-label="Close recharge dialog" @click="showRecharge = false"><X class="h-5 w-5" /></button>
                </div>
                <form :action="actions.checkout" method="GET" class="p-5">
                    <label><span class="mb-2 block text-sm font-black text-slate-700">Recharge amount</span><input v-model.number="amount" class="customer-input" type="number" name="recharge_amount" :min="minRechargeAmount" required /></label>
                    <p class="mt-2 text-xs font-bold text-slate-500">Minimum recharge: {{ money(minRechargeAmount) }}</p>
                    <dl class="mt-5 divide-y divide-slate-200 rounded-lg bg-slate-50 px-4">
                        <Row label="Base amount" :value="money(amount)" />
                        <Row label="GST (18%)" :value="money(gst)" />
                        <Row label="Total payable" :value="money(total)" strong />
                    </dl>
                    <button class="mt-5 w-full rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50" :disabled="amount < minRechargeAmount">Proceed to payment</button>
                </form>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Download, Plus, WalletCards, X } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Transaction = { id: number; type: string; amount: number; remark: string | null; reference_id: string | null; created_at: string; receipt_url: string | null };
const props = defineProps<{ balance: number; minRechargeAmount: number; openRecharge: boolean; transactions: { data: Transaction[]; links: Array<{ url: string | null; label: string; active: boolean }> }; actions: { checkout: string } }>();
const showRecharge = ref(props.openRecharge);
const amount = ref(props.minRechargeAmount);
const gst = computed(() => Number(amount.value || 0) * 0.18);
const total = computed(() => Number(amount.value || 0) + gst.value);
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const Row = defineComponent({ props: { label: { type: String, required: true }, value: { type: String, required: true }, strong: { type: Boolean, default: false } }, setup(p) { return () => h('div', { class: 'flex items-center justify-between gap-4 py-3' }, [h('dt', { class: p.strong ? 'font-black text-slate-950' : 'text-sm font-bold text-slate-500' }, p.label), h('dd', { class: p.strong ? 'text-lg font-black text-teal-700' : 'text-sm font-black text-slate-950' }, p.value)]); } });
</script>
