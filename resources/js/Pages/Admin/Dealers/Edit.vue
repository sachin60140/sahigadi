<template>
    <Head :title="`Edit ${dealer.name}`" />

    <AdminLayout :title="`Edit ${dealer.name}`" eyebrow="Dealer operations">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Account maintenance</p>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-black text-slate-950">{{ dealer.name }}</h2>
                        <StatusBadge :status="dealer.status" />
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        {{ dealer.dealer_unique_id || 'Dealer account' }} / profile {{ dealer.profile_completion }}% complete
                    </p>
                </div>
                <Link :href="actions.back" class="w-fit rounded-lg border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">Back to details</Link>
            </div>
        </section>

        <DealerForm
            :form="form"
            :plans="plans"
            :documents="dealer.documents"
            :cancel-url="actions.back"
            submit-label="Update Dealer"
            is-edit
            @submit="submit"
        />
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DealerForm from './Partials/DealerForm.vue';

const props = defineProps<{
    dealer: {
        dealer_unique_id?: string | null;
        name: string;
        email: string;
        phone?: string | null;
        company_name?: string | null;
        address?: string | null;
        city?: string | null;
        state?: string | null;
        pincode?: string | null;
        status: string;
        gst_number?: string | null;
        kyc_document_type?: string | null;
        kyc_document_number?: string | null;
        pan_number?: string | null;
        profile_completion: number;
        documents: { kyc?: string | null; pan?: string | null; gst?: string | null };
    };
    plans: Array<{ id: number; name: string; price: number; listing_limit: number; duration_days: number }>;
    actions: { update: string; back: string };
}>();

const form = useForm({
    name: props.dealer.name,
    email: props.dealer.email,
    phone: props.dealer.phone || '',
    company_name: props.dealer.company_name || '',
    address: props.dealer.address || '',
    city: props.dealer.city || '',
    state: props.dealer.state || '',
    pincode: props.dealer.pincode || '',
    password: '',
    status: props.dealer.status,
    kyc_document_type: props.dealer.kyc_document_type || 'aadhar',
    kyc_document_number: props.dealer.kyc_document_number || '',
    kyc_document: null as File | null,
    pan_number: props.dealer.pan_number || '',
    pan_document: null as File | null,
    gst_number: props.dealer.gst_number || '',
    gst_document: null as File | null,
    assign_plan: '',
});

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'PUT' })).post(props.actions.update, {
        forceFormData: true,
        preserveScroll: true,
    });
};

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize',
                badgeProps.status === 'approved'
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : badgeProps.status === 'pending'
                        ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                        : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.status);
    },
});
</script>
