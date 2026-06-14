<template>
    <Head title="Create Customer Listing" />

    <AdminLayout title="Create Customer Listing" eyebrow="Customer inventory">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Manual listing</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Add a customer car listing.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Create listings for individual sellers with owner contact details and vehicle information.
                    </p>
                </div>
                <Link :href="actions.back" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                    Back
                </Link>
            </div>
        </section>

        <ListingForm :form="form" :options="options" :cancel-url="actions.back" submit-label="Create Listing" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ListingForm from './Partials/ListingForm.vue';

const props = defineProps<{
    options: Record<string, any>;
    actions: { store: string; back: string };
}>();

const form = useForm({
    title: '',
    brand_id: '',
    model: '',
    year: '',
    fuel_type: '',
    transmission: '',
    km_driven: '',
    price: '',
    city: '',
    registration_number: '',
    owners: 1,
    owner_name: '',
    owner_phone: '',
    whatsapp_number: '',
    status: 'pending',
    images: [] as File[],
});

const submit = () => {
    form.post(props.actions.store, {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>
