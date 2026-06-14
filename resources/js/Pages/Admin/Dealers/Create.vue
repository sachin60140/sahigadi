<template>
    <Head title="Add Dealer" />

    <AdminLayout title="Add Dealer" eyebrow="Dealer operations">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">New network account</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Create a complete dealer profile.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Add account, showroom, identity and tax information, then optionally activate a subscription plan.
                    </p>
                </div>
                <Link :href="actions.back" class="w-fit rounded-lg border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">Back</Link>
            </div>
        </section>

        <DealerForm :form="form" :plans="plans" :cancel-url="actions.back" submit-label="Create Dealer" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DealerForm from './Partials/DealerForm.vue';

const props = defineProps<{
    plans: Array<{ id: number; name: string; price: number; listing_limit: number; duration_days: number }>;
    actions: { store: string; back: string };
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company_name: '',
    address: '',
    city: '',
    state: '',
    pincode: '',
    password: '',
    status: 'pending',
    kyc_document_type: 'aadhar',
    kyc_document_number: '',
    kyc_document: null as File | null,
    pan_number: '',
    pan_document: null as File | null,
    gst_number: '',
    gst_document: null as File | null,
    assign_plan: '',
});

const submit = () => form.post(props.actions.store, { forceFormData: true, preserveScroll: true });
</script>
