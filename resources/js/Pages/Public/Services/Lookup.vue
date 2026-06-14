<template>
    <Head>
        <title>{{ service.seoTitle }}</title>
        <meta head-key="description" name="description" :content="service.seoDescription" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
        <link head-key="canonical" rel="canonical" :href="service.canonical" />
        <meta head-key="og-title" property="og:title" :content="service.seoTitle" />
        <meta head-key="og-description" property="og:description" :content="service.seoDescription" />
    </Head>

    <component :is="layoutComponent" v-bind="layoutProps">
        <section :class="service.customerAuthenticated ? '' : 'border-b border-slate-200 bg-[linear-gradient(135deg,#f6fbff_0%,#edfbf6_54%,#fff8ef_100%)]'">
            <div :class="service.customerAuthenticated ? '' : 'mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8'">
                <div v-if="!service.customerAuthenticated" class="grid gap-7 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-end">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-lg border border-teal-100 bg-white px-3 py-2 text-xs font-black uppercase tracking-wide text-teal-700 shadow-sm">
                            <ShieldCheck class="h-4 w-4" />
                            {{ service.eyebrow }}
                        </span>
                        <h1 class="mt-5 max-w-4xl text-3xl font-black leading-tight text-slate-950 sm:text-5xl">{{ service.title }}</h1>
                        <p class="mt-5 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">{{ service.description }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70">
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600">Report charge</p>
                        <p class="mt-2 text-4xl font-black text-slate-950">{{ money(service.charge) }}</p>
                        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">One report, securely processed. Cached reports may be shown when available.</p>
                    </div>
                </div>
            </div>
        </section>

        <section :class="service.customerAuthenticated ? '' : 'bg-[#f7f9fb] py-10 sm:py-12'">
            <div :class="service.customerAuthenticated ? '' : 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8'">
                <div v-if="flashError" class="mb-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    {{ flashError }}
                </div>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-7" @submit.prevent="submit">
                        <div class="flex items-start gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700">
                                <CarFront class="h-6 w-6" />
                            </span>
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Start a new report</p>
                                <h2 class="mt-1 text-2xl font-black text-slate-950">{{ service.title }}</h2>
                                <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ service.description }}</p>
                            </div>
                        </div>

                        <div class="mt-7">
                            <label :for="service.numberField" class="mb-2 block text-sm font-black text-slate-800">{{ service.numberLabel }}</label>
                            <div class="relative">
                                <Hash class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                                <input
                                    :id="service.numberField"
                                    v-model="form[service.numberField]"
                                    type="text"
                                    class="sg-input min-h-12 pl-12 uppercase"
                                    :placeholder="service.placeholder"
                                    maxlength="20"
                                    required
                                    autocomplete="off"
                                />
                            </div>
                            <p v-if="fieldError(service.numberField)" class="mt-2 text-sm font-bold text-red-600">{{ fieldError(service.numberField) }}</p>
                        </div>

                        <div v-if="service.requiresGuestDetails" class="mt-5 grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-black text-slate-800">Your name</span>
                                <input v-model="form.customer_name" type="text" class="sg-input min-h-12" placeholder="Enter your name" autocomplete="name" required />
                                <span v-if="form.errors.customer_name" class="mt-2 block text-sm font-bold text-red-600">{{ form.errors.customer_name }}</span>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-black text-slate-800">Phone number</span>
                                <input v-model="form.customer_phone" type="tel" class="sg-input min-h-12" placeholder="Enter phone number" autocomplete="tel" required />
                                <span v-if="form.errors.customer_phone" class="mt-2 block text-sm font-bold text-red-600">{{ form.errors.customer_phone }}</span>
                            </label>
                            <label class="block md:col-span-2">
                                <span class="mb-2 block text-sm font-black text-slate-800">Email address <span class="font-semibold text-slate-400">(optional)</span></span>
                                <input v-model="form.customer_email" type="email" class="sg-input min-h-12" placeholder="you@example.com" autocomplete="email" />
                                <span v-if="form.errors.customer_email" class="mt-2 block text-sm font-bold text-red-600">{{ form.errors.customer_email }}</span>
                            </label>
                        </div>

                        <div class="mt-6 flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Report charge</p>
                                <p class="mt-1 text-2xl font-black text-slate-950">{{ money(service.charge) }}</p>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-black text-white shadow-sm transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                                <Search v-else class="h-5 w-5" />
                                {{ form.processing ? 'Processing...' : service.submitLabel }}
                            </button>
                        </div>
                    </form>

                    <aside class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600">What this report helps verify</p>
                        <div class="mt-5 grid gap-4">
                            <div v-for="feature in service.features" :key="feature" class="flex gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700">
                                    <Check class="h-4 w-4" />
                                </span>
                                <p class="pt-1 text-sm font-bold leading-6 text-slate-700">{{ feature }}</p>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <div class="flex gap-3">
                                <LockKeyhole class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />
                                <p class="text-sm font-medium leading-6 text-slate-600">Payments are verified server-side before a fresh report is generated.</p>
                            </div>
                        </div>
                    </aside>
                </div>

                <section v-if="service.customerAuthenticated" class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <History class="h-5 w-5 text-teal-700" />
                        <h2 class="text-xl font-black text-slate-950">Your search history</h2>
                    </div>
                    <div v-if="history.length" class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-y border-slate-200 bg-slate-50 text-xs font-black uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Vehicle</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="item in history" :key="item.id">
                                    <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-600">{{ formatDate(item.created_at) }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 font-black uppercase text-slate-950">{{ item.number }}</td>
                                    <td class="px-4 py-4">
                                        <span :class="item.is_success ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-700'" class="inline-flex rounded-md px-2.5 py-1 text-xs font-black">
                                            {{ item.is_success ? 'Success' : 'Failed' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Link v-if="item.view_url" :href="item.view_url" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="View report">
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                            <a v-if="item.pdf_url" :href="item.pdf_url" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-red-600 hover:bg-red-50" title="Download PDF">
                                                <Download class="h-4 w-4" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="mt-5 rounded-lg border border-dashed border-slate-300 px-5 py-10 text-center">
                        <Search class="mx-auto h-8 w-8 text-slate-300" />
                        <p class="mt-3 text-sm font-bold text-slate-500">No search history yet.</p>
                    </div>
                </section>
            </div>
        </section>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { CarFront, Check, Download, Eye, Hash, History, LoaderCircle, LockKeyhole, Search, ShieldCheck } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

type Service = {
    key: string;
    eyebrow: string;
    title: string;
    description: string;
    numberField: 'registration_number' | 'vehicle_number';
    numberLabel: string;
    placeholder: string;
    submitLabel: string;
    actionUrl: string;
    charge: number;
    requiresGuestDetails: boolean;
    customerAuthenticated: boolean;
    seoTitle: string;
    seoDescription: string;
    canonical: string;
    features: string[];
};

type HistoryItem = {
    id: number;
    number: string;
    is_success: boolean;
    created_at: string | null;
    view_url: string | null;
    pdf_url: string | null;
};

const props = defineProps<{ service: Service; history: HistoryItem[] }>();
const page = usePage();
const layoutComponent = computed(() => props.service.customerAuthenticated ? CustomerLayout : PublicLayout);
const layoutProps = computed(() => props.service.customerAuthenticated ? { title: props.service.title, eyebrow: props.service.eyebrow } : {});
const flashError = computed(() => (page.props as any).flash?.error || '');

const form = useForm<Record<string, string>>({
    registration_number: '',
    vehicle_number: '',
    customer_name: '',
    customer_phone: '',
    customer_email: '',
});

const submit = () => form.post(props.service.actionUrl, { preserveScroll: true });
const fieldError = (key: string) => (form.errors as Record<string, string>)[key];
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;
const formatDate = (value: string | null) => value
    ? new Intl.DateTimeFormat('en-IN', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
    : 'N/A';
</script>
