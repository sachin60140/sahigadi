<template>
    <Head title="Add Subscription Plan" />
    <AdminLayout title="Add Subscription Plan" eyebrow="Revenue settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">New dealer offer</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-950">Create a subscription plan.</h2>
            <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Set the price, listing capacity and access period offered to dealers.</p>
        </section>
        <PlanForm :form="form" :cancel-url="actions.back" submit-label="Create Plan" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import PlanForm from './Partials/PlanForm.vue';

const props = defineProps<{ actions: { store: string; back: string } }>();
const form = useForm({ name: '', price: '', listing_limit: '', duration_days: '', description: '', is_active: true });
const submit = () => form.post(props.actions.store, { preserveScroll: true });
</script>
