<template>
    <Head title="Add Brand" />

    <AdminLayout title="Add Brand" eyebrow="Catalog settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">New taxonomy entry</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-950">Add a vehicle brand.</h2>
            <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Create the reusable brand identity shown in listing forms and inventory filters.</p>
        </section>
        <BrandForm :form="form" :cancel-url="actions.back" submit-label="Create Brand" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import BrandForm from './Partials/BrandForm.vue';

const props = defineProps<{ actions: { store: string; back: string } }>();
const form = useForm({ name: '', logo: null as File | null, is_active: true });
const submit = () => form.post(props.actions.store, { forceFormData: true, preserveScroll: true });
</script>
