<template>
    <Head>
        <title>RC Search Result</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <component :is="layoutComponent" v-bind="layoutProps">
        <section :class="customer ? '' : 'bg-[#f7f9fb] px-4 py-10 sm:py-14'">
            <div :class="customer ? '' : 'mx-auto max-w-5xl'">
                <div v-if="!vehicleSearch" class="rounded-lg border border-red-100 bg-white p-7 text-center shadow-sm">
                    <CircleAlert class="mx-auto h-10 w-10 text-red-500" />
                    <h1 class="mt-4 text-2xl font-black text-slate-950">Report unavailable</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ message || 'No vehicle report was returned.' }}</p>
                </div>

                <template v-else>
                    <div :class="vehicleSearch.is_success ? 'border-teal-100 bg-teal-50' : 'border-red-100 bg-red-50'" class="rounded-lg border p-5 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <CircleCheck v-if="vehicleSearch.is_success" class="mt-0.5 h-7 w-7 shrink-0 text-teal-700" />
                                <CircleAlert v-else class="mt-0.5 h-7 w-7 shrink-0 text-red-600" />
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide" :class="vehicleSearch.is_success ? 'text-teal-700' : 'text-red-700'">
                                        {{ vehicleSearch.is_success ? 'Vehicle details found' : 'No records found' }}
                                    </p>
                                    <h1 class="mt-1 text-2xl font-black uppercase text-slate-950 sm:text-3xl">{{ vehicleSearch.registration_number }}</h1>
                                    <p v-if="vehicleSearch.customer_name" class="mt-2 text-sm font-semibold text-slate-600">Requested by {{ vehicleSearch.customer_name }}</p>
                                </div>
                            </div>
                            <a v-if="pdfUrl && vehicleSearch.is_success" :href="pdfUrl" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-black text-white hover:bg-slate-800">
                                <Download class="h-4 w-4" />
                                Download PDF
                            </a>
                        </div>
                    </div>

                    <div v-if="cached" class="mt-5 flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                        <Clock3 class="h-5 w-5 shrink-0" />
                        Retrieved from a verified report generated within the last 24 hours.
                    </div>

                    <div v-if="success === false" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ message || vehicleSearch.error_message || 'The search could not be completed.' }}
                    </div>

                    <div v-if="vehicleSearch.is_success && entries.length" class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div v-for="[key, value] in entries" :key="key" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-400">{{ label(key) }}</p>
                            <p class="mt-2 break-words text-sm font-black text-slate-900">{{ displayValue(value) }}</p>
                        </div>
                    </div>

                    <div v-else-if="vehicleSearch.is_success" class="mt-6 rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <Info class="mx-auto h-8 w-8 text-slate-400" />
                        <p class="mt-3 text-sm font-bold text-slate-600">No detailed vehicle fields were available.</p>
                    </div>

                    <div v-if="!vehicleSearch.is_success" class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold leading-7 text-slate-700">{{ vehicleSearch.error_message || message || 'No vehicle details were found for this registration number.' }}</p>
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
import { ArrowLeft, CircleAlert, CircleCheck, Clock3, Download, Info } from '@lucide/vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps<{
    vehicleSearch: any | null;
    cached: boolean;
    success: boolean;
    message?: string | null;
    indexUrl: string;
    pdfUrl?: string | null;
}>();

const page = usePage();
const customer = computed(() => Boolean((page.props as any).auth?.customer));
const layoutComponent = computed(() => customer.value ? CustomerLayout : PublicLayout);
const layoutProps = computed(() => customer.value ? { title: 'RC search result', eyebrow: 'Vehicle verification' } : {});
const entries = computed(() => Object.entries(props.vehicleSearch?.vehicle_data || {}).filter(([, value]) => value !== null && value !== ''));
const label = (key: string) => key.replaceAll('_', ' ').replace(/\b\w/g, (character) => character.toUpperCase());
const displayValue = (value: unknown) => typeof value === 'object' ? JSON.stringify(value) : String(value ?? 'N/A');
</script>
