<template>
    <Head :title="`Edit ${plan.name}`" />
    <AdminLayout :title="`Edit ${plan.name}`" eyebrow="Revenue settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Promotion maintenance</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-950">Update {{ plan.name }}.</h2>
            <p class="mt-2 text-sm font-semibold text-slate-600">{{ plan.active_featured_listings_count }} active / {{ plan.featured_listings_count }} total linked promotions</p>
        </section>
        <FeaturedPlanForm :form="form" :cancel-url="actions.back" submit-label="Update Featured Plan" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import FeaturedPlanForm from './Partials/FeaturedPlanForm.vue';

const props = defineProps<{
    plan: { name: string; duration_days: number; price: number; is_active: boolean; featured_listings_count: number; active_featured_listings_count: number };
    actions: { update: string; back: string };
}>();
const form = useForm({ name: props.plan.name, duration_days: props.plan.duration_days, price: props.plan.price, is_active: props.plan.is_active });
const submit = () => form.put(props.actions.update, { preserveScroll: true });
</script>
