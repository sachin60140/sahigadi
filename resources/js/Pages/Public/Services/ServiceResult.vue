<template>
    <Head>
        <title>{{ serviceTitle }} Result</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <component :is="layoutComponent" v-bind="layoutProps">
        <section :class="customer ? '' : 'bg-[#f7f9fb] px-4 py-10 sm:py-14'">
            <div :class="customer ? '' : 'mx-auto max-w-7xl'">
                <div v-if="!report" class="rounded-lg border border-red-100 bg-white p-7 text-center shadow-sm">
                    <CircleAlert class="mx-auto h-10 w-10 text-red-500" />
                    <h1 class="mt-4 text-2xl font-black text-slate-950">Report unavailable</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ message || 'No service-history report was returned.' }}</p>
                </div>

                <template v-else>
                    <header :class="report.is_success ? 'border-teal-100 bg-teal-50' : 'border-red-100 bg-red-50'" class="rounded-lg border p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-start gap-3">
                                <CircleCheck v-if="report.is_success" class="mt-0.5 h-7 w-7 shrink-0 text-teal-700" />
                                <CircleAlert v-else class="mt-0.5 h-7 w-7 shrink-0 text-red-600" />
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide" :class="report.is_success ? 'text-teal-700' : 'text-red-700'">
                                        {{ report.is_success ? 'Verified service report' : 'Search unsuccessful' }}
                                    </p>
                                    <h1 class="mt-1 text-2xl font-black text-slate-950 sm:text-3xl">{{ serviceTitle }}</h1>
                                    <p class="mt-2 text-sm font-semibold text-slate-600">
                                        Vehicle <span class="font-black uppercase text-slate-950">{{ report.vehicle_number }}</span>
                                        <span v-if="report.customer_name"> · Requested by {{ report.customer_name }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span v-if="report.is_success" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-teal-200 bg-white px-4 py-2.5 text-sm font-black text-teal-700">
                                    <BadgeCheck class="h-4 w-4" />
                                    {{ records.length }} records
                                </span>
                                <a v-if="pdfUrl && report.is_success" :href="pdfUrl" class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-black text-white hover:bg-slate-800">
                                    <Download class="h-4 w-4" />
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    </header>

                    <div v-if="cached" class="mt-5 flex flex-col gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3 text-sm font-bold text-amber-800">
                            <Clock3 class="h-5 w-5 shrink-0" />
                            Retrieved from a report generated within the last 24 hours.
                        </div>
                        <button type="button" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-black text-amber-900 hover:bg-amber-100" :disabled="freshForm.processing" @click="freshSearch">
                            <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': freshForm.processing }" />
                            Run fresh search
                        </button>
                    </div>

                    <div v-if="success === false" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ message || report.error_message || 'The search could not be completed.' }}
                    </div>

                    <template v-if="report.is_success && records.length">
                        <section class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-4">
                                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Service timeline</p>
                                <h2 class="mt-1 text-xl font-black text-slate-950">Recorded workshop visits</h2>
                            </div>

                            <div class="grid gap-4 p-4 md:hidden">
                                <article v-for="(record, index) in records" :key="record.id || index" class="rounded-lg border border-slate-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Service date</p>
                                            <p class="mt-1 text-sm font-black text-slate-950">{{ formatDate(record.svc_date) }}</p>
                                        </div>
                                        <span class="rounded-md bg-teal-50 px-2.5 py-1 text-xs font-black text-teal-700">{{ record.service_cate || record.work_type || 'Service' }}</span>
                                    </div>
                                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                                        <div v-for="column in mobileColumns" :key="column.key">
                                            <dt class="text-xs font-black uppercase tracking-wide text-slate-400">{{ column.label }}</dt>
                                            <dd class="mt-1 break-words text-sm font-bold text-slate-700">{{ formatCell(record, column) }}</dd>
                                        </div>
                                    </dl>
                                </article>
                            </div>

                            <div class="hidden overflow-x-auto md:block">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="bg-slate-50 text-xs font-black uppercase text-slate-500">
                                        <tr>
                                            <th v-for="column in columns" :key="column.key" class="whitespace-nowrap px-4 py-3">{{ column.label }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="(record, index) in records" :key="record.id || index" class="align-top hover:bg-slate-50">
                                            <td v-for="column in columns" :key="column.key" class="max-w-[260px] whitespace-nowrap px-4 py-4 font-semibold text-slate-700">
                                                {{ formatCell(record, column) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section v-if="summary.length && variant !== 'generic'" class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <p class="text-xs font-black uppercase tracking-wide text-orange-600">Bill summary</p>
                            <h2 class="mt-1 text-xl font-black text-slate-950">Spend by service category</h2>
                            <div class="mt-5 overflow-x-auto">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="border-y border-slate-200 bg-slate-50 text-xs font-black uppercase text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3">Category</th>
                                            <th class="px-4 py-3 text-right">Parts</th>
                                            <th class="px-4 py-3 text-right">Labour</th>
                                            <th class="px-4 py-3 text-right">Total</th>
                                            <th class="px-4 py-3 text-right">Visits</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="item in summary" :key="item.category">
                                            <td class="px-4 py-4 font-black text-slate-900">{{ item.category }}</td>
                                            <td class="px-4 py-4 text-right font-semibold text-slate-600">{{ money(item.parts) }}</td>
                                            <td class="px-4 py-4 text-right font-semibold text-slate-600">{{ money(item.labour) }}</td>
                                            <td class="px-4 py-4 text-right font-black text-teal-700">{{ money(item.total) }}</td>
                                            <td class="px-4 py-4 text-right font-black text-slate-900">{{ item.count }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </template>

                    <div v-else-if="report.is_success" class="mt-6 rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <Info class="mx-auto h-8 w-8 text-slate-400" />
                        <p class="mt-3 text-sm font-bold text-slate-600">No service records were found for this vehicle.</p>
                    </div>

                    <div v-if="!report.is_success" class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold leading-7 text-slate-700">{{ report.error_message || message || 'No service history was found for this vehicle.' }}</p>
                        <ul class="mt-4 grid gap-2 text-sm font-semibold text-slate-600">
                            <li class="flex gap-2"><Check class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />Confirm the registration number is correct.</li>
                            <li class="flex gap-2"><Check class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />The vehicle may not have authorized workshop records.</li>
                            <li class="flex gap-2"><Check class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />Some vehicle brands or models may not be supported.</li>
                        </ul>
                    </div>
                </template>

                <div class="mt-6">
                    <Link :href="indexUrl" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm hover:bg-slate-50">
                        <ArrowLeft class="h-4 w-4" />
                        Search another vehicle
                    </Link>
                </div>
            </div>
        </section>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, BadgeCheck, Check, CircleAlert, CircleCheck, Clock3, Download, Info, RefreshCw } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

type Column = { key: string; label: string; type?: 'date' | 'money' | 'mileage' };
const props = defineProps<{
    report: any | null;
    cached: boolean;
    success: boolean;
    message?: string | null;
    variant: 'generic' | 'maruti' | 'mahindra';
    serviceTitle: string;
    indexUrl: string;
    pdfUrl?: string | null;
    freshSearchUrl: string;
}>();

const page = usePage();
const customer = computed(() => Boolean((page.props as any).auth?.customer));
const layoutComponent = computed(() => customer.value ? CustomerLayout : PublicLayout);
const layoutProps = computed(() => customer.value ? { title: props.serviceTitle, eyebrow: 'Service report' } : {});
const records = computed<any[]>(() => props.report?.records || []);

const columnSets: Record<string, Column[]> = {
    generic: [
        { key: 'svc_date', label: 'Date', type: 'date' },
        { key: 'dealer_name', label: 'Dealer' },
        { key: 'work_type', label: 'Work type' },
        { key: 'mileage', label: 'Mileage', type: 'mileage' },
        { key: 'net_bill_amt', label: 'Bill amount', type: 'money' },
    ],
    maruti: [
        { key: 'svc_date', label: 'Date', type: 'date' },
        { key: 'service_cate', label: 'Service type' },
        { key: 'dealer_name', label: 'Dealer' },
        { key: 'register_no', label: 'Job card' },
        { key: 'repair_order_no', label: 'RO number' },
        { key: 'total_amount', label: 'Total', type: 'money' },
        { key: 'mileage', label: 'Mileage', type: 'mileage' },
    ],
    mahindra: [
        { key: 'svc_date', label: 'Date', type: 'date' },
        { key: 'service_cate', label: 'Category' },
        { key: 'work_type', label: 'Work type' },
        { key: 'dealer_name', label: 'Dealer' },
        { key: 'dealer_address', label: 'Location' },
        { key: 'register_no', label: 'Job card' },
        { key: 'repair_order_no', label: 'RO number' },
        { key: 'repair_order_bill_no', label: 'Bill number' },
        { key: 'repair_order_bill_date', label: 'Bill date', type: 'date' },
        { key: 'service_assistant_name', label: 'Assistant' },
        { key: 'total_amount', label: 'Total', type: 'money' },
        { key: 'status', label: 'Status' },
        { key: 'mileage', label: 'Mileage', type: 'mileage' },
    ],
};

const columns = computed(() => columnSets[props.variant]);
const mobileColumns = computed(() => columns.value.filter((column) => column.key !== 'svc_date').slice(0, 8));
const formatDate = (value: unknown) => value
    ? new Intl.DateTimeFormat('en-IN', { dateStyle: 'medium' }).format(new Date(String(value)))
    : 'N/A';
const money = (value: unknown) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const formatCell = (record: any, column: Column) => {
    const value = record?.[column.key];
    if (column.type === 'date') return formatDate(value);
    if (column.type === 'money') return money(value);
    if (column.type === 'mileage') return value ? `${new Intl.NumberFormat('en-IN').format(Number(value))} km` : 'N/A';
    return value || 'N/A';
};

const summary = computed(() => {
    const grouped = new Map<string, { category: string; parts: number; labour: number; total: number; count: number }>();
    records.value.forEach((record) => {
        const category = record.service_cate || 'Unknown';
        const current = grouped.get(category) || { category, parts: 0, labour: 0, total: 0, count: 0 };
        current.parts += Number(record.part_amount || 0);
        current.labour += Number(record.labour_amount || 0);
        current.total += Number(record.total_amount || 0);
        current.count += 1;
        grouped.set(category, current);
    });
    return Array.from(grouped.values());
});

const freshForm = useForm({
    vehicle_number: props.report?.vehicle_number || '',
    customer_name: props.report?.customer_name || '',
    customer_phone: props.report?.customer_phone || '',
    customer_email: props.report?.customer_email || '',
    force_fresh: '1',
});
const freshSearch = () => freshForm.post(props.freshSearchUrl);
</script>
