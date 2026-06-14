<template>
    <Head :title="`Enquiry #${enquiry.id}`" />

    <AdminLayout :title="`Enquiry #${enquiry.id}`" eyebrow="Buyer lead details">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link href="/admin/enquiries" class="inline-flex rounded-lg border border-slate-200 px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                        Back to enquiries
                    </Link>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-black text-slate-950">{{ enquiry.customer_name }}</h2>
                        <StatusBadge :status="enquiry.status" />
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Received {{ enquiry.created_at || 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-if="enquiry.status === 'new'"
                        type="button"
                        class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-800"
                        @click="markContacted"
                    >
                        Mark as contacted
                    </button>
                    <a
                        v-if="enquiry.whatsapp_url"
                        :href="enquiry.whatsapp_url"
                        target="_blank"
                        rel="noreferrer"
                        class="rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600"
                    >
                        Send WhatsApp
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.75fr]">
            <div class="grid content-start gap-5">
                <Panel title="Customer information" eyebrow="Buyer">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <InfoItem label="Name" :value="enquiry.customer_name" />
                        <InfoItem label="Received" :value="enquiry.created_at || 'N/A'" />
                        <InfoItem label="Phone">
                            <a v-if="enquiry.phone_url" :href="enquiry.phone_url" class="font-black text-teal-700">{{ enquiry.customer_phone }}</a>
                            <span v-else>{{ enquiry.customer_phone || 'N/A' }}</span>
                        </InfoItem>
                        <InfoItem label="Email">
                            <a v-if="enquiry.email_url" :href="enquiry.email_url" class="break-all font-black text-teal-700">{{ enquiry.customer_email }}</a>
                            <span v-else>N/A</span>
                        </InfoItem>
                        <InfoItem label="IP address" :value="enquiry.ip_address || 'N/A'" />
                    </div>
                    <div v-if="enquiry.message" class="mt-5 border-t border-slate-100 pt-5">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Customer message</p>
                        <p class="mt-3 whitespace-pre-wrap rounded-lg bg-slate-50 p-4 text-sm font-semibold leading-7 text-slate-700">{{ enquiry.message }}</p>
                    </div>
                </Panel>

                <Panel title="Vehicle details" eyebrow="Requested listing">
                    <template v-if="enquiry.vehicle">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-2xl font-black text-slate-950">{{ enquiry.vehicle.title }}</h3>
                                    <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-black capitalize text-slate-700">
                                        {{ enquiry.vehicle.type }} listing
                                    </span>
                                </div>
                                <p class="mt-2 text-sm font-bold text-slate-600">
                                    {{ enquiry.vehicle.brand || 'No brand' }} /
                                    {{ enquiry.vehicle.year || 'N/A' }} /
                                    {{ formatNumber(enquiry.vehicle.km_driven) }} km /
                                    {{ formatSpec(enquiry.vehicle.fuel_type) }}
                                </p>
                                <p class="mt-4 text-2xl font-black text-teal-700">{{ formatCurrency(enquiry.vehicle.price) }}</p>
                                <p class="mt-2 text-sm font-bold uppercase text-slate-500">
                                    Registration: {{ enquiry.vehicle.registration_number || 'N/A' }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Link :href="enquiry.vehicle.admin_url" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                                    Open in admin
                                </Link>
                                <a
                                    v-if="enquiry.vehicle.public_url"
                                    :href="enquiry.vehicle.public_url"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-black text-white transition hover:bg-teal-700"
                                >
                                    View public page
                                </a>
                            </div>
                        </div>
                    </template>
                    <div v-else class="rounded-lg border border-red-100 bg-red-50 p-4">
                        <p class="font-black text-red-700">The associated vehicle is no longer available.</p>
                        <p class="mt-1 text-sm font-semibold text-red-600">The enquiry remains available for customer follow-up.</p>
                    </div>
                </Panel>
            </div>

            <div class="grid content-start gap-5">
                <Panel title="Lead actions" eyebrow="Follow-up">
                    <div v-if="enquiry.status === 'contacted'" class="rounded-lg border border-teal-100 bg-teal-50 p-4 text-sm font-black text-teal-700">
                        This enquiry is marked as contacted.
                    </div>
                    <button
                        v-else
                        type="button"
                        class="w-full rounded-lg bg-teal-700 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-800"
                        @click="markContacted"
                    >
                        Mark as contacted
                    </button>
                    <a
                        v-if="enquiry.whatsapp_url"
                        :href="enquiry.whatsapp_url"
                        target="_blank"
                        rel="noreferrer"
                        class="mt-3 flex w-full justify-center rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-black text-teal-700 transition hover:bg-white"
                    >
                        Continue on WhatsApp
                    </a>
                </Panel>

                <Panel v-if="enquiry.listed_by === 'dealer'" title="Dealer information" eyebrow="Listing owner">
                    <template v-if="enquiry.dealer">
                        <p class="text-xl font-black text-slate-950">{{ enquiry.dealer.company_name || enquiry.dealer.name }}</p>
                        <p v-if="enquiry.dealer.company_name" class="mt-1 text-sm font-bold text-slate-500">{{ enquiry.dealer.name }}</p>
                        <a v-if="enquiry.dealer.phone" :href="`tel:${enquiry.dealer.phone}`" class="mt-4 block text-sm font-black text-teal-700">{{ enquiry.dealer.phone }}</a>
                        <a v-if="enquiry.dealer.email" :href="`mailto:${enquiry.dealer.email}`" class="mt-2 block break-all text-sm font-black text-teal-700">{{ enquiry.dealer.email }}</a>
                        <Link :href="enquiry.dealer.show_url" class="mt-5 inline-flex rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                            View dealer
                        </Link>
                    </template>
                    <div v-else class="rounded-lg border border-red-100 bg-red-50 p-4 text-sm font-black text-red-700">The associated dealer account has been deleted.</div>
                </Panel>

                <Panel v-else title="Listing owner" eyebrow="Customer sale">
                    <p class="text-xl font-black text-slate-950">{{ enquiry.vehicle?.owner_name || 'Customer-owned listing' }}</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">This enquiry came from an individual seller listing.</p>
                </Panel>
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Enquiry = {
    id: number;
    customer_name: string;
    customer_email?: string | null;
    customer_phone?: string | null;
    status: string;
    created_at?: string | null;
    message?: string | null;
    ip_address?: string | null;
    phone_url?: string | null;
    email_url?: string | null;
    whatsapp_url?: string | null;
    listed_by: string;
    vehicle?: {
        type: string;
        title: string;
        brand?: string | null;
        year?: number | null;
        fuel_type?: string | null;
        km_driven?: number | null;
        price: number;
        registration_number?: string | null;
        owner_name?: string | null;
        admin_url: string;
        public_url?: string | null;
    } | null;
    dealer?: {
        name: string;
        company_name?: string | null;
        phone?: string | null;
        email?: string | null;
        show_url: string;
    } | null;
    actions: { contacted: string };
};

const props = defineProps<{ enquiry: Enquiry }>();

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;
const formatNumber = (value?: number | string | null) => value ? new Intl.NumberFormat('en-IN').format(Number(value)) : '0';
const formatSpec = (value?: string | null) => value ? value.charAt(0).toUpperCase() + value.slice(1) : 'N/A';
const markContacted = () => router.post(props.enquiry.actions.contacted, {}, { preserveScroll: true });

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize',
                badgeProps.status === 'new'
                    ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                    : 'bg-teal-50 text-teal-700 ring-1 ring-teal-100',
            ],
        }, badgeProps.status);
    },
});

const Panel = defineComponent({
    props: {
        title: { type: String, required: true },
        eyebrow: { type: String, required: true },
    },
    setup(panelProps, { slots }) {
        return () => h('section', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-teal-700' }, panelProps.eyebrow),
            h('h3', { class: 'mt-1 text-xl font-black text-slate-950' }, panelProps.title),
            h('div', { class: 'mt-5' }, slots.default?.()),
        ]);
    },
});

const InfoItem = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, default: '' },
    },
    setup(itemProps, { slots }) {
        return () => h('div', { class: 'rounded-lg border border-slate-100 bg-slate-50 p-4' }, [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-slate-400' }, itemProps.label),
            h('div', { class: 'mt-2 text-sm font-black text-slate-950' }, slots.default ? slots.default() : itemProps.value),
        ]);
    },
});
</script>
