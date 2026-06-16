<template>
    <Head :title="`Enquiry from ${enquiry.customer_name}`" />
    <DealerLayout title="Enquiry Details" eyebrow="Buyer lead">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-sm font-bold text-slate-500">Received {{ enquiry.created_at }}</p><h2 class="mt-1 text-3xl font-semibold text-slate-950">{{ enquiry.customer_name }}</h2></div>
            <div class="flex flex-wrap gap-2">
                <a :href="enquiry.whatsapp_url" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-700">WhatsApp</a>
                <a :href="enquiry.call_url" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white">Call buyer</a>
            </div>
        </div>

        <section class="mt-5 grid gap-5 lg:grid-cols-2">
            <InfoCard title="Customer information" :rows="customerRows" />
            <InfoCard title="Vehicle interest" :rows="carRows">
                <a v-if="enquiry.car_url" :href="enquiry.car_url" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex text-sm font-semibold text-teal-700">Open public listing</a>
            </InfoCard>
        </section>

        <section v-if="enquiry.message" class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Customer message</p>
            <p class="mt-3 whitespace-pre-wrap text-sm font-semibold leading-7 text-slate-700">{{ enquiry.message }}</p>
        </section>

        <div class="mt-5 flex flex-wrap gap-3">
            <button type="button" class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50" :disabled="enquiry.status === 'contacted'" @click="markContacted">{{ enquiry.status === 'contacted' ? 'Already contacted' : 'Mark as contacted' }}</button>
            <Link :href="actions.index" class="rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to enquiries</Link>
        </div>
    </DealerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DealerLayout from '@/Layouts/DealerLayout.vue';

const props = defineProps<{
    enquiry: { customer_name: string; customer_phone: string; customer_email: string | null; status: string; created_at: string; car_title: string | null; car_url: string | null; car_price: number; message: string | null; call_url: string; whatsapp_url: string };
    actions: { index: string; contacted: string };
}>();
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN').format(value || 0)}`;
const customerRows = computed(() => [['Name', props.enquiry.customer_name], ['Email', props.enquiry.customer_email || 'Not provided'], ['Phone', props.enquiry.customer_phone], ['Status', props.enquiry.status]]);
const carRows = computed(() => [['Car', props.enquiry.car_title || 'Car unavailable'], ['Price', money(props.enquiry.car_price)]]);
const markContacted = () => router.post(props.actions.contacted, {}, { preserveScroll: true });
const InfoCard = defineComponent({
    props: { title: { type: String, required: true }, rows: { type: Array as () => string[][], required: true } },
    setup(cardProps, { slots }) {
        return () => h('section', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('h3', { class: 'text-xl font-semibold text-slate-950' }, cardProps.title),
            h('dl', { class: 'mt-4 divide-y divide-slate-100' }, cardProps.rows.map(([label, value]) => h('div', { class: 'grid gap-1 py-3 sm:grid-cols-[140px_1fr]' }, [h('dt', { class: 'text-sm font-bold text-slate-500' }, label), h('dd', { class: 'break-words text-sm font-semibold capitalize text-slate-950' }, value)]))),
            slots.default?.(),
        ]);
    },
});
</script>
