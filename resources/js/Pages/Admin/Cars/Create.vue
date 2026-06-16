<template>
    <Head title="Add Car" />

    <AdminLayout title="Add New Car" eyebrow="Inventory operations">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Create listing</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Add a dealer car manually.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Assign the listing to a dealer, add pricing and upload inventory photos in one place.
                    </p>
                </div>
                <Link :href="actions.back" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Back
                </Link>
            </div>
        </section>

        <CarForm :form="form" :options="options" :cancel-url="actions.back" submit-label="Add Car" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CarForm from './Partials/CarForm.vue';

const props = defineProps<{
    options: Record<string, any>;
    actions: { store: string; back: string };
}>();

const form = useForm({
    dealer_id: '',
    status: 'pending',
    title: '',
    brand_id: '',
    model: '',
    year: '',
    fuel_type: '',
    transmission: '',
    km_driven: '',
    price: '',
    city: '',
    latitude: '',
    longitude: '',
    registration_number: '',
    owners: 1,
    description: '',
    images: [] as File[],
});

const submit = () => {
    form.post(props.actions.store, {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>
