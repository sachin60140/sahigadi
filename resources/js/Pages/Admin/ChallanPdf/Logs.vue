<template>
    <Head title="Challan PDF Search Logs" />
    <AdminLayout title="Challan PDF Search Logs" eyebrow="Service operations">
        <ChallanPdfTabs />

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Metric label="Total requests" :value="stats.total" />
            <Metric label="Successful" :value="stats.successful" tone="teal" />
            <Metric label="Failed" :value="stats.failed" tone="red" />
            <Metric label="Service revenue" :value="money(stats.revenue)" tone="blue" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <form class="grid flex-1 gap-3 sm:grid-cols-2 xl:grid-cols-[1.3fr_150px_150px_160px_160px_auto]" @submit.prevent="filter">
                    <Field label="Search">
                        <input v-model="form.search" class="admin-input" type="search" placeholder="Vehicle, name or phone" />
                    </Field>
                    <Field label="Channel">
                        <select v-model="form.channel" class="admin-input">
                            <option value="">All channels</option>
                            <option value="customer">Customer</option>
                            <option value="dealer">Dealer</option>
                        </select>
                    </Field>
                    <Field label="Status">
                        <select v-model="form.status" class="admin-input">
                            <option value="">All statuses</option>
                            <option value="success">Successful</option>
                            <option value="failed">Failed</option>
                        </select>
                    </Field>
                    <Field label="From">
                        <input v-model="form.from_date" class="admin-input" type="date" />
                    </Field>
                    <Field label="To">
                        <input v-model="form.to_date" class="admin-input" type="date" />
                    </Field>
                    <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-1">
                        <button class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-teal-700">
                            Filter
                        </button>
                        <Link
                            href="/admin/challan-pdf/logs"
                            class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50"
                        >
                            Clear
                        </Link>
                    </div>
                </form>
                <a
                    :href="actions.export"
                    class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-black text-white transition hover:bg-teal-800"
                >
                    Export CSV
                </a>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Vehicle</th>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Channel</th>
                            <th class="px-5 py-3">Outcome</th>
                            <th class="px-5 py-3">Charge</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-black uppercase text-slate-950">{{ log.vehicle_number }}</p>
                                <p class="mt-1 text-xs font-black text-slate-400">#{{ log.id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ log.user_name || 'Unknown user' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ [log.user_id, log.user_phone].filter(Boolean).join(' / ') || 'No account details' }}
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md bg-blue-50 px-2.5 py-1 text-xs font-black capitalize text-blue-700 ring-1 ring-blue-100">
                                    {{ log.channel }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <Status :success="log.is_success" />
                                <p v-if="log.error_message" class="mt-2 max-w-[240px] truncate text-xs font-bold text-red-600">
                                    {{ log.error_message }}
                                </p>
                            </td>
                            <td class="px-5 py-4 font-black text-slate-950">{{ money(log.charge_amount) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ log.created_at || 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a
                                        v-if="log.is_success && log.pdf_url"
                                        :href="log.pdf_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                                    >
                                        PDF
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-black text-white transition hover:bg-teal-700"
                                        @click="selectedLog = log"
                                    >
                                        Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!logs.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No Challan PDF logs found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try changing or clearing the filters.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="logs.links" />
            </div>
        </section>

        <div v-if="selectedLog" class="fixed inset-0 z-50">
            <button
                type="button"
                class="absolute inset-0 bg-slate-950/60"
                aria-label="Close API details"
                @click="selectedLog = null"
            ></button>
            <aside
                class="absolute inset-y-0 right-0 flex w-full max-w-2xl flex-col bg-white shadow-2xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="log-details-title"
            >
                <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">API request #{{ selectedLog.id }}</p>
                        <h2 id="log-details-title" class="mt-1 text-xl font-black uppercase text-slate-950">
                            {{ selectedLog.vehicle_number }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-slate-200 text-xl font-bold text-slate-600 transition hover:bg-slate-50"
                        aria-label="Close API details"
                        @click="selectedLog = null"
                    >
                        &times;
                    </button>
                </header>
                <div class="flex-1 overflow-y-auto px-5 py-5 sm:px-6">
                    <div v-if="selectedLog.error_message" class="mb-5 rounded-lg border border-red-100 bg-red-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-red-700">Error message</p>
                        <p class="mt-2 break-words text-sm font-semibold leading-6 text-red-700">{{ selectedLog.error_message }}</p>
                    </div>
                    <JsonBlock title="API request" :value="selectedLog.api_request" />
                    <JsonBlock class="mt-5" title="API response" :value="selectedLog.api_response" />
                </div>
            </aside>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ChallanPdfTabs from '@/Components/Admin/ChallanPdfTabs.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Log = {
    id: number;
    vehicle_number: string;
    channel: string;
    user_name: string | null;
    user_id: string | null;
    user_phone: string | null;
    is_success: boolean;
    charge_amount: number;
    error_message: string | null;
    pdf_url: string | null;
    api_request: unknown;
    api_response: unknown;
    created_at: string | null;
};

const props = defineProps<{
    logs: { data: Log[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: { search: string; channel: string; status: string; from_date: string; to_date: string };
    stats: { total: number; successful: number; failed: number; revenue: number };
    actions: { settings: string; export: string };
}>();

const form = reactive({ ...props.filters });
const selectedLog = ref<Log | null>(null);
const money = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const filter = () => {
    const params: Record<string, string> = {};
    Object.entries(form).forEach(([key, value]) => {
        if (value) params[key] = value;
    });
    router.get('/admin/challan-pdf/logs', params, { preserveState: true, preserveScroll: true });
};

const Field = defineComponent({
    props: { label: { type: String, required: true } },
    setup(fieldProps, { slots }) {
        return () => h('label', [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, fieldProps.label),
            slots.default?.(),
        ]);
    },
});

const Metric = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: [String, Number], required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(metricProps) {
        const tones: Record<string, string> = {
            slate: 'border-slate-200 bg-white',
            teal: 'border-teal-100 bg-teal-50',
            red: 'border-red-100 bg-red-50',
            blue: 'border-blue-100 bg-blue-50',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', tones[metricProps.tone]] }, [
            h('p', { class: 'text-2xl font-black text-slate-950' }, typeof metricProps.value === 'number'
                ? new Intl.NumberFormat('en-IN').format(metricProps.value)
                : metricProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide text-slate-500' }, metricProps.label),
        ]);
    },
});

const Status = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(statusProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black',
                statusProps.success
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, statusProps.success ? 'Successful' : 'Failed');
    },
});

const JsonBlock = defineComponent({
    props: {
        title: { type: String, required: true },
        value: { type: null, required: true },
    },
    setup(jsonProps) {
        const formatted = () => {
            if (jsonProps.value === null || jsonProps.value === undefined || jsonProps.value === '') return 'No data recorded.';
            if (typeof jsonProps.value === 'string') return jsonProps.value;
            return JSON.stringify(jsonProps.value, null, 2);
        };
        return () => h('section', [
            h('h3', { class: 'text-sm font-black text-slate-950' }, jsonProps.title),
            h('pre', {
                class: 'mt-2 max-h-[420px] overflow-auto whitespace-pre-wrap break-words rounded-lg bg-slate-950 p-4 text-xs leading-6 text-slate-100',
            }, formatted()),
        ]);
    },
});
</script>
