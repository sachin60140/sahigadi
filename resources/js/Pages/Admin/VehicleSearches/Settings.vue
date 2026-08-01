<template>
    <Head title="RC Search Pricing" />

    <AdminLayout title="RC Search Pricing" eyebrow="Service settings">
        <RcSearchTabs />

        <section class="mt-5 grid gap-3 sm:grid-cols-3">
            <MetricTile label="Dealer searches" :value="stats.total" />
            <MetricTile label="Successful" :value="stats.successful" tone="teal" />
            <MetricTile label="Recorded revenue" :value="formatCurrency(stats.revenue)" tone="blue" />
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" @submit.prevent="submit">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Charge configuration</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-950">Set customer and dealer RC lookup prices.</h2>
                <p class="mt-2 max-w-2xl text-sm font-semibold leading-7 text-slate-600">
                    These values control the service charge applied to new searches. Existing transaction records are not changed.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <label>
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Customer charge</span>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center font-semibold text-slate-500">Rs</span>
                            <input v-model="form.charge" class="admin-input pl-11" type="number" min="0" step="0.01" required />
                        </div>
                        <p v-if="form.errors.charge" class="mt-2 text-xs font-bold text-red-600">{{ form.errors.charge }}</p>
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Dealer charge</span>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center font-semibold text-slate-500">Rs</span>
                            <input v-model="form.dealer_charge" class="admin-input pl-11" type="number" min="0" step="0.01" required />
                        </div>
                        <p v-if="form.errors.dealer_charge" class="mt-2 text-xs font-bold text-red-600">{{ form.errors.dealer_charge }}</p>
                    </label>
                </div>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Dealer API</p>
                            <h3 class="mt-1 text-lg font-bold text-slate-950">Partner API access</h3>
                            <p class="mt-1 max-w-xl text-sm font-medium leading-6 text-slate-600">
                                Lets approved dealers call the RC lookup from their own systems. Each successful lookup is debited from their wallet; cached repeats within 24 hours are free.
                            </p>
                        </div>
                        <span class="rounded-md px-2.5 py-1 text-xs font-semibold" :class="form.api_enabled ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'">
                            {{ form.api_enabled ? 'Live' : 'Disabled' }}
                        </span>
                    </div>

                    <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <input v-model="form.api_enabled" type="checkbox" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-teal-700 focus:ring-teal-600" />
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Enable the dealer vehicle-search API</span>
                            <span class="mt-1 block text-xs font-medium text-slate-500">Master switch. Individual dealers must also be enabled on their profile page.</span>
                        </span>
                    </label>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="mb-2 block text-sm font-semibold text-slate-700">API charge per lookup</span>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center font-semibold text-slate-500">Rs</span>
                                <input v-model="form.api_charge" class="admin-input pl-11" type="number" min="0" step="0.01" required />
                            </div>
                            <p class="mt-2 text-xs font-medium text-slate-500">Charged per successful API lookup, separate from the dealer panel price.</p>
                            <p v-if="form.errors.api_charge" class="mt-2 text-xs font-bold text-red-600">{{ form.errors.api_charge }}</p>
                        </label>
                        <div class="grid grid-cols-2 gap-3 self-start">
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-lg font-bold text-slate-950">{{ api.enabled_dealers }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">Dealers enabled</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-lg font-bold text-slate-950">{{ api.calls }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">API lookups</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="mt-6 rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save pricing' }}
                </button>
            </form>

            <aside class="rounded-lg border border-orange-100 bg-orange-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-orange-700">Pricing note</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-950">Keep margins and gateway fees in view.</h3>
                <p class="mt-3 text-sm font-semibold leading-7 text-slate-600">
                    Customer pricing should cover payment processing and API costs. Dealer pricing is debited from the dealer workflow separately.
                </p>
            </aside>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RcSearchTabs from '@/Components/Admin/RcSearchTabs.vue';

const props = defineProps<{
    charges: { customer: number; dealer: number; api: number };
    api: { enabled: boolean; enabled_dealers: number; calls: number; revenue: number };
    stats: { total: number; successful: number; revenue: number };
    actions: { update: string };
}>();

const form = useForm({
    charge: props.charges.customer,
    dealer_charge: props.charges.dealer,
    api_charge: props.charges.api,
    api_enabled: props.api.enabled,
});

const submit = () => form.post(props.actions.update, { preserveScroll: true });
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: [String, Number], required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const colors: Record<string, string> = {
            slate: 'border-slate-200 bg-white text-slate-950',
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            blue: 'border-blue-100 bg-blue-50 text-blue-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', colors[tileProps.tone] || colors.slate] }, [
            h('p', { class: 'text-2xl font-semibold' }, typeof tileProps.value === 'number' ? new Intl.NumberFormat('en-IN').format(tileProps.value) : tileProps.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});
</script>
