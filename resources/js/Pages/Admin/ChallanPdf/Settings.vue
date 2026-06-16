<template>
    <Head title="Challan PDF Service" />
    <AdminLayout title="Challan PDF Service" eyebrow="Service operations">
        <ChallanPdfTabs />

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Metric label="Total requests" :value="stats.total" />
            <Metric label="Successful" :value="stats.successful" tone="teal" />
            <Metric label="Failed" :value="stats.failed" tone="red" />
            <Metric label="Service revenue" :value="money(stats.revenue)" tone="blue" />
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Charge configuration</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-950">Control availability and lookup pricing.</h2>
                <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                    These prices apply to new customer payments and dealer wallet debits. Existing records remain unchanged.
                </p>

                <label class="mt-6 flex cursor-pointer items-start justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <span>
                        <span class="block text-sm font-semibold text-slate-950">Enable Challan PDF service</span>
                        <span class="mt-1 block text-sm font-semibold leading-6 text-slate-500">
                            Allow customers and dealers to request paid Challan PDF reports.
                        </span>
                    </span>
                    <span class="relative mt-0.5 inline-flex shrink-0">
                        <input v-model="form.is_challan_pdf_active" type="checkbox" class="peer sr-only" />
                        <span class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-teal-700 peer-focus-visible:ring-2 peer-focus-visible:ring-teal-600 peer-focus-visible:ring-offset-2"></span>
                        <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                    </span>
                </label>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <PriceField
                        v-model="form.challan_pdf_charge"
                        label="Customer charge"
                        :error="form.errors.challan_pdf_charge"
                    />
                    <PriceField
                        v-model="form.dealer_challan_pdf_charge"
                        label="Dealer charge"
                        :error="form.errors.dealer_challan_pdf_charge"
                    />
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button
                        type="submit"
                        class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save settings' }}
                    </button>
                    <Link
                        :href="actions.logs"
                        class="rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        View search logs
                    </Link>
                </div>
            </form>

            <aside class="rounded-lg border p-5" :class="settings.active ? 'border-teal-100 bg-teal-50' : 'border-orange-100 bg-orange-50'">
                <p class="text-xs font-semibold uppercase tracking-wide" :class="settings.active ? 'text-teal-700' : 'text-orange-700'">
                    Current service status
                </p>
                <h3 class="mt-2 text-xl font-semibold text-slate-950">
                    {{ settings.active ? 'Challan PDF is accepting requests.' : 'Challan PDF is currently paused.' }}
                </h3>
                <p class="mt-3 text-sm font-semibold leading-7 text-slate-600">
                    Failed requests remain visible in the log for support review and API troubleshooting.
                </p>
            </aside>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ChallanPdfTabs from '@/Components/Admin/ChallanPdfTabs.vue';

const props = defineProps<{
    settings: { active: boolean; customerCharge: number; dealerCharge: number };
    stats: { total: number; successful: number; failed: number; revenue: number };
    actions: { update: string; logs: string };
}>();

const form = useForm({
    is_challan_pdf_active: props.settings.active,
    challan_pdf_charge: props.settings.customerCharge,
    dealer_challan_pdf_charge: props.settings.dealerCharge,
});

const submit = () => form.post(props.actions.update, { preserveScroll: true });
const money = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

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
            h('p', { class: 'text-2xl font-semibold text-slate-950' }, typeof metricProps.value === 'number'
                ? new Intl.NumberFormat('en-IN').format(metricProps.value)
                : metricProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500' }, metricProps.label),
        ]);
    },
});

const PriceField = defineComponent({
    props: {
        label: { type: String, required: true },
        modelValue: { type: [String, Number], required: true },
        error: { type: String, default: '' },
    },
    emits: ['update:modelValue'],
    setup(fieldProps, { emit }) {
        return () => h('label', [
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, fieldProps.label),
            h('div', { class: 'relative' }, [
                h('span', { class: 'pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center font-semibold text-slate-500' }, 'Rs'),
                h('input', {
                    class: 'admin-input pl-11',
                    type: 'number',
                    min: '0',
                    step: '0.01',
                    required: true,
                    value: fieldProps.modelValue,
                    onInput: (event: Event) => emit('update:modelValue', (event.target as HTMLInputElement).value),
                }),
            ]),
            fieldProps.error ? h('p', { class: 'mt-2 text-xs font-bold text-red-600' }, fieldProps.error) : null,
        ]);
    },
});
</script>
