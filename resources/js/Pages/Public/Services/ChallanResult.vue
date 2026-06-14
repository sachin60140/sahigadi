<template>
    <Head>
        <title>E-Challan Result</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <PublicLayout>
        <section class="bg-[#f7f9fb] px-4 py-10 sm:py-14">
            <div class="mx-auto max-w-6xl">
                <div v-if="!challanSearch" class="rounded-lg border border-red-100 bg-white p-7 text-center shadow-sm">
                    <CircleAlert class="mx-auto h-10 w-10 text-red-500" />
                    <h1 class="mt-4 text-2xl font-black text-slate-950">Report unavailable</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ message || 'No challan report was returned.' }}</p>
                </div>

                <template v-else>
                    <header :class="hasPending ? 'border-orange-200 bg-orange-50' : 'border-teal-100 bg-teal-50'" class="rounded-lg border p-5 sm:p-6">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <TriangleAlert v-if="hasPending" class="mt-0.5 h-7 w-7 shrink-0 text-orange-600" />
                                <CircleCheck v-else class="mt-0.5 h-7 w-7 shrink-0 text-teal-700" />
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide" :class="hasPending ? 'text-orange-700' : 'text-teal-700'">
                                        {{ hasPending ? 'Pending challans found' : 'No pending challans' }}
                                    </p>
                                    <h1 class="mt-1 text-2xl font-black uppercase text-slate-950 sm:text-3xl">{{ challanSearch.vehicle_number }}</h1>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-white/80 bg-white px-4 py-3 text-center shadow-sm">
                                    <p class="text-xs font-black uppercase text-slate-400">Challans</p>
                                    <p class="mt-1 text-xl font-black text-slate-950">{{ challanSearch.challan_count || 0 }}</p>
                                </div>
                                <div class="rounded-lg border border-white/80 bg-white px-4 py-3 text-center shadow-sm">
                                    <p class="text-xs font-black uppercase text-slate-400">Amount</p>
                                    <p class="mt-1 text-xl font-black text-orange-600">{{ money(challanSearch.total_amount) }}</p>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div v-if="cached" class="mt-5 flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                        <Clock3 class="h-5 w-5 shrink-0" />
                        Retrieved from a report generated within the last 24 hours.
                    </div>

                    <div v-if="success === false" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ message || challanSearch.error_message || 'The search could not be completed.' }}
                    </div>

                    <section v-if="challans.length" class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-4">
                            <p class="text-xs font-black uppercase tracking-wide text-orange-600">Traffic records</p>
                            <h2 class="mt-1 text-xl font-black text-slate-950">Challan details</h2>
                        </div>

                        <div class="grid gap-4 p-4 md:hidden">
                            <article v-for="(challan, index) in challans" :key="challan.challanNo || index" class="rounded-lg border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Challan number</p>
                                        <p class="mt-1 text-sm font-black text-slate-950">{{ challan.challanNo || 'N/A' }}</p>
                                    </div>
                                    <span :class="challan.status === 'Paid' ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-700'" class="rounded-md px-2.5 py-1 text-xs font-black">
                                        {{ challan.status || 'Unknown' }}
                                    </span>
                                </div>
                                <dl class="mt-4 grid gap-3">
                                    <div>
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Date</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-700">{{ formatDate(challan.dateChallan) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Offence</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-700">{{ offence(challan) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Location</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-700">{{ challan.locationChallan || 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-400">Amount</dt>
                                        <dd class="mt-1 text-sm font-black text-orange-600">{{ money(challan.amountChallan) }}</dd>
                                    </div>
                                </dl>
                            </article>
                        </div>

                        <div class="hidden overflow-x-auto md:block">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-50 text-xs font-black uppercase text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Challan number</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Location</th>
                                        <th class="px-4 py-3">Offence</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(challan, index) in challans" :key="challan.challanNo || index">
                                        <td class="whitespace-nowrap px-4 py-4 font-black text-slate-900">{{ challan.challanNo || 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-600">{{ formatDate(challan.dateChallan) }}</td>
                                        <td class="max-w-[220px] px-4 py-4 font-semibold text-slate-600">{{ challan.locationChallan || 'N/A' }}</td>
                                        <td class="max-w-[280px] px-4 py-4 font-semibold text-slate-600">{{ offence(challan) }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 text-right font-black text-orange-600">{{ money(challan.amountChallan) }}</td>
                                        <td class="px-4 py-4">
                                            <span :class="challan.status === 'Paid' ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-700'" class="rounded-md px-2.5 py-1 text-xs font-black">
                                                {{ challan.status || 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div v-else-if="challanSearch.is_success" class="mt-6 rounded-lg border border-teal-100 bg-white p-8 text-center shadow-sm">
                        <CircleCheck class="mx-auto h-9 w-9 text-teal-700" />
                        <h2 class="mt-3 text-xl font-black text-slate-950">No pending challans found</h2>
                        <p class="mt-2 text-sm font-semibold text-slate-600">The report did not return any outstanding traffic challans for this vehicle.</p>
                    </div>

                    <div v-if="!challanSearch.is_success" class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold text-slate-700">{{ challanSearch.error_message || message || 'No challan records were found for this vehicle.' }}</p>
                    </div>
                </template>

                <div class="mt-6">
                    <Link :href="indexUrl" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm hover:bg-slate-50">
                        <ArrowLeft class="h-4 w-4" />
                        Check another vehicle
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CircleAlert, CircleCheck, Clock3, TriangleAlert } from '@lucide/vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps<{
    challanSearch: any | null;
    cached: boolean;
    success: boolean;
    message?: string | null;
    indexUrl: string;
}>();

const challans = computed<any[]>(() => props.challanSearch?.challan_data || []);
const hasPending = computed(() => Number(props.challanSearch?.total_amount || 0) > 0);
const money = (value: unknown) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;
const formatDate = (value: unknown) => value
    ? new Intl.DateTimeFormat('en-IN', { dateStyle: 'medium' }).format(new Date(String(value)))
    : 'N/A';
const offence = (challan: any) => challan?.detailsViolation?.[0]?.offence || 'N/A';
</script>
