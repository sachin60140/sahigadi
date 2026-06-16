<template>
    <Head title="Challan PDF" />
    <CustomerLayout title="Challan PDF" eyebrow="Document lookup">
        <div class="mx-auto max-w-6xl">
            <div class="mb-5 flex flex-wrap gap-2">
                <Link :href="actions.index" class="rounded-lg px-4 py-2.5 text-sm font-semibold" :class="!showHistory ? 'bg-teal-700 text-white' : 'border border-slate-200 bg-white text-slate-700'">New search</Link>
                <Link :href="actions.history" class="rounded-lg px-4 py-2.5 text-sm font-semibold" :class="showHistory ? 'bg-teal-700 text-white' : 'border border-slate-200 bg-white text-slate-700'">Search history</Link>
            </div>

            <section v-if="!showHistory" class="grid gap-5 lg:grid-cols-[1fr_320px]">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700"><FileText class="h-6 w-6" /></span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">PDF generation</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Search by challan number</h2>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">The wallet is charged only when a valid PDF is generated successfully.</p>
                        </div>
                    </div>

                    <form class="mt-7" @submit.prevent="submitSearch">
                        <label>
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Challan number</span>
                            <input v-model="form.vehicle_number" class="customer-input uppercase" maxlength="20" placeholder="Enter challan number" required />
                            <span v-if="form.errors.vehicle_number" class="mt-1 block text-xs font-bold text-red-600">{{ form.errors.vehicle_number }}</span>
                        </label>
                        <button class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-800 disabled:opacity-50" :disabled="form.processing || !service.active || service.wallet_balance < service.charge">
                            <Search class="h-4 w-4" />
                            {{ form.processing ? 'Generating...' : 'Search and generate PDF' }}
                        </button>
                    </form>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-lg bg-slate-950 p-5 text-white">
                        <p class="text-xs font-semibold uppercase tracking-wide text-teal-300">Search charge</p>
                        <p class="mt-2 text-3xl font-semibold">{{ money(service.charge) }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-300">Deducted only on success</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Wallet balance</p>
                        <p class="mt-2 text-2xl font-semibold text-teal-700">{{ money(service.wallet_balance) }}</p>
                        <Link v-if="service.wallet_balance < service.charge" :href="actions.wallet" class="mt-4 inline-flex w-full justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white">Recharge wallet</Link>
                    </div>
                    <p v-if="!service.active" class="rounded-lg border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">This service is currently unavailable.</p>
                </aside>
            </section>

            <section v-else class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4"><p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Activity</p><h2 class="mt-1 text-xl font-semibold text-slate-950">Search history</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Challan number</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Charge</th><th class="px-5 py-3 text-right">PDF</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="record in history.data" :key="record.id" class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-semibold text-slate-500">{{ record.created_at }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-950">{{ record.vehicle_number }}</td>
                                <td class="px-5 py-4"><span class="rounded-md px-2.5 py-1 text-xs font-semibold" :class="record.is_success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100'">{{ record.is_success ? 'Success' : 'Failed' }}</span></td>
                                <td class="px-5 py-4 font-semibold text-slate-700">{{ money(record.charge_amount) }}</td>
                                <td class="px-5 py-4 text-right"><a v-if="record.is_success && record.pdf_url" :href="record.pdf_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"><Download class="h-3.5 w-3.5" /> Download</a><span v-else class="text-slate-300">-</span></td>
                            </tr>
                            <tr v-if="!history.data.length"><td colspan="5" class="px-5 py-14 text-center"><FileText class="mx-auto h-10 w-10 text-slate-300" /><p class="mt-3 font-semibold text-slate-950">No searches yet</p></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="history.links" /></div>
            </section>
        </div>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Download, FileText, Search } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

const props = defineProps<{ showHistory: boolean; service: { active: boolean; charge: number; wallet_balance: number }; history: any; actions: { search: string; index: string; history: string; wallet: string } }>();
const form = useForm({ vehicle_number: '' });
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const submitSearch = () => form.post(props.actions.search, { preserveScroll: true });
</script>
