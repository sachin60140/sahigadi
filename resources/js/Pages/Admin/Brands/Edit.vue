<template>
    <Head :title="`Edit ${brand.name}`" />

    <AdminLayout :title="`Edit ${brand.name}`" eyebrow="Catalog settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Brand maintenance</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-950">Update {{ brand.name }}.</h2>
            <p class="mt-2 text-sm font-semibold text-slate-600">{{ brand.inventory_count }} linked vehicle records / slug: {{ brand.slug }}</p>
        </section>
        <BrandForm :form="form" :cancel-url="actions.back" :current-logo-url="brand.logo_url" submit-label="Update Brand" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import BrandForm from './Partials/BrandForm.vue';

const props = defineProps<{
    brand: { name: string; slug: string; logo_url?: string | null; is_active: boolean; inventory_count: number };
    actions: { update: string; back: string };
}>();

const form = useForm({ name: props.brand.name, logo: null as File | null, is_active: props.brand.is_active });
const submit = () => {
    form.transform((data) => ({ ...data, _method: 'PUT' })).post(props.actions.update, {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>
