<template>
    <Head :title="`RC Search ${search.registration_number}`" />

    <AdminLayout title="Dealer RC Search Details" eyebrow="RC operations">
        <RcSearchTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <Link :href="search.actions.back" class="inline-flex rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to searches</Link>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-teal-700">Registration record</p>
                    <h2 class="mt-2 text-3xl font-semibold uppercase text-slate-950">{{ search.registration_number }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        {{ search.vehicle || 'Vehicle details unavailable' }}
                        <span v-if="search.dealer"> / {{ search.dealer.name }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <StatusBadge :success="search.is_success" />
                    <a v-if="search.is_success" :href="search.actions.pdf" class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">Download PDF</a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <SummaryTile label="Search charge" :value="formatCurrency(search.charge_amount)" />
            <SummaryTile label="RC status" :value="search.rc_status || (search.is_success ? 'Available' : 'Failed')" tone="teal" />
            <SummaryTile label="Search date" :value="search.created_at || 'N/A'" tone="blue" />
            <SummaryTile label="Record depth" :value="search.has_extended_record ? 'Extended dealer record' : 'Search snapshot'" tone="orange" />
        </section>

        <section v-if="search.error_message" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Lookup error</p>
            <p class="mt-2 text-sm font-bold leading-6 text-red-700">{{ search.error_message }}</p>
        </section>

        <section v-if="search.is_success && !search.has_extended_record" class="mt-5 rounded-lg border border-orange-100 bg-orange-50 px-5 py-4">
            <p class="text-sm font-semibold text-orange-800">The extended dealer vehicle record is unavailable.</p>
            <p class="mt-1 text-sm font-semibold text-orange-700">The original RC search snapshot is shown below, so the result remains usable.</p>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-2">
            <DetailPanel v-for="section in sections" :key="section.title" :title="section.title" :rows="section.rows" />
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RcSearchTabs from '@/Components/Admin/RcSearchTabs.vue';

type ExtendedRecord = {
    father_name?: string | null;
    mobile_number?: string | null;
    rto_location?: string | null;
    vehicle_class?: string | null;
    vehicle_category?: string | null;
    variant?: string | null;
    color?: string | null;
    seats?: string | number | null;
    fitness_date?: string | null;
    insurance_provider?: string | null;
    puc_number?: string | null;
    blacklist_status?: string | null;
    financed?: boolean | null;
    lender_name?: string | null;
    norms_type?: string | null;
    cubic_capacity?: string | number | null;
    is_commercial?: boolean | null;
    permit_number?: string | null;
    permit_type?: string | null;
    permit_validity?: string | null;
};

type SearchDetail = {
    registration_number: string;
    owner_name?: string | null;
    vehicle?: string | null;
    fuel_type?: string | null;
    rc_status?: string | null;
    is_success: boolean;
    charge_amount: number;
    error_message?: string | null;
    created_at?: string | null;
    address?: string | null;
    registration_date?: string | null;
    insurance_date?: string | null;
    insurance_policy_number?: string | null;
    puc_validity?: string | null;
    chassis_number?: string | null;
    engine_number?: string | null;
    has_extended_record: boolean;
    extended?: ExtendedRecord | null;
    dealer?: { name: string; phone?: string | null; show_url: string } | null;
    actions: { back: string; pdf: string };
};

const props = defineProps<{ search: SearchDetail }>();
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const yesNo = (value?: boolean | null) => value === null || value === undefined ? 'N/A' : (value ? 'Yes' : 'No');

const sections = computed(() => {
    const extended = props.search.extended;

    return [
        {
            title: 'Owner and registration',
            rows: [
                { label: 'Owner name', value: props.search.owner_name },
                { label: 'Father name', value: extended?.father_name },
                { label: 'Address', value: props.search.address },
                { label: 'Mobile number', value: extended?.mobile_number },
                { label: 'Registration date', value: props.search.registration_date },
                { label: 'RTO location', value: extended?.rto_location },
            ],
        },
        {
            title: 'Vehicle identity',
            rows: [
                { label: 'Make and model', value: props.search.vehicle },
                { label: 'Variant', value: extended?.variant },
                { label: 'Vehicle class', value: extended?.vehicle_class },
                { label: 'Category', value: extended?.vehicle_category },
                { label: 'Fuel type', value: props.search.fuel_type },
                { label: 'Color', value: extended?.color },
                { label: 'Seats', value: extended?.seats },
                { label: 'Emission norm', value: extended?.norms_type },
                { label: 'Cubic capacity', value: extended?.cubic_capacity },
                { label: 'Commercial', value: yesNo(extended?.is_commercial) },
            ],
        },
        {
            title: 'Identifiers',
            rows: [
                { label: 'Engine number', value: props.search.engine_number },
                { label: 'Chassis number', value: props.search.chassis_number },
                { label: 'RC status', value: props.search.rc_status },
                { label: 'Blacklist status', value: extended?.blacklist_status },
                { label: 'Financed', value: yesNo(extended?.financed) },
                { label: 'Lender', value: extended?.lender_name },
            ],
        },
        {
            title: 'Documents and validity',
            rows: [
                { label: 'Insurance provider', value: extended?.insurance_provider },
                { label: 'Insurance valid till', value: props.search.insurance_date },
                { label: 'Policy number', value: props.search.insurance_policy_number },
                { label: 'Fitness valid till', value: extended?.fitness_date },
                { label: 'PUC number', value: extended?.puc_number },
                { label: 'PUC valid till', value: props.search.puc_validity },
                { label: 'Permit number', value: extended?.permit_number },
                { label: 'Permit type', value: extended?.permit_type },
                { label: 'Permit valid till', value: extended?.permit_validity },
            ],
        },
    ];
});

const SummaryTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const colors: Record<string, string> = {
            slate: 'border-slate-200 bg-white',
            teal: 'border-teal-100 bg-teal-50',
            blue: 'border-blue-100 bg-blue-50',
            orange: 'border-orange-100 bg-orange-50',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-slate-500' }, tileProps.label),
            h('p', { class: 'mt-2 text-lg font-semibold text-slate-950' }, tileProps.value),
        ]);
    },
});

const StatusBadge = defineComponent({
    props: { success: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-3 py-2 text-xs font-semibold',
                badgeProps.success ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.success ? 'Successful lookup' : 'Failed lookup');
    },
});

const DetailPanel = defineComponent({
    props: {
        title: { type: String, required: true },
        rows: { type: Array as () => Array<{ label: string; value: unknown }>, required: true },
    },
    setup(panelProps) {
        const display = (value: unknown) => value === null || value === undefined || value === '' ? 'N/A' : String(value);
        return () => h('article', { class: 'overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm' }, [
            h('div', { class: 'border-b border-slate-100 px-5 py-4' }, [
                h('h3', { class: 'text-lg font-semibold text-slate-950' }, panelProps.title),
            ]),
            h('dl', { class: 'divide-y divide-slate-100' }, panelProps.rows.map((row) => h('div', {
                class: 'grid gap-1 px-5 py-3 sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-4',
            }, [
                h('dt', { class: 'text-xs font-semibold uppercase tracking-wide text-slate-500' }, row.label),
                h('dd', { class: 'break-words text-sm font-bold text-slate-800' }, display(row.value)),
            ]))),
        ]);
    },
});
</script>
