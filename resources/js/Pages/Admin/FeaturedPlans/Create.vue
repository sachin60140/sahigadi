<template>
    <Head title="Add Featured Plan" />
    <AdminLayout title="Add Featured Plan" eyebrow="Revenue settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-teal-700">New promotion offer</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Create a featured listing plan.</h2>
            <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Set how long a paid vehicle promotion runs and what the seller pays.</p>
        </section>
        <FeaturedPlanForm :form="form" :cancel-url="actions.back" submit-label="Create Featured Plan" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import FeaturedPlanForm from './Partials/FeaturedPlanForm.vue';

const props = defineProps<{ actions: { store: string; back: string } }>();
const form = useForm({ name: '', duration_days: '', price: '', is_active: true });
const submit = () => form.post(props.actions.store, { preserveScroll: true });
</script>
