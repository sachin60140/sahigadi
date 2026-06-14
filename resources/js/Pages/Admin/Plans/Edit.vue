<template>
    <Head :title="`Edit ${plan.name}`" />
    <AdminLayout :title="`Edit ${plan.name}`" eyebrow="Revenue settings">
        <SettingsTabs />
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-teal-700">Subscription maintenance</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Update {{ plan.name }}.</h2>
            <p class="mt-2 text-sm font-semibold text-slate-600">{{ plan.active_subscriptions_count }} active / {{ plan.subscriptions_count }} total linked subscriptions</p>
        </section>
        <PlanForm :form="form" :cancel-url="actions.back" submit-label="Update Plan" @submit="submit" />
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SettingsTabs from '@/Components/Admin/SettingsTabs.vue';
import PlanForm from './Partials/PlanForm.vue';

const props = defineProps<{
    plan: { name: string; price: number; listing_limit: number; duration_days: number; description?: string | null; is_active: boolean; subscriptions_count: number; active_subscriptions_count: number };
    actions: { update: string; back: string };
}>();
const form = useForm({
    name: props.plan.name,
    price: props.plan.price,
    listing_limit: props.plan.listing_limit,
    duration_days: props.plan.duration_days,
    description: props.plan.description || '',
    is_active: props.plan.is_active,
});
const submit = () => form.put(props.actions.update, { preserveScroll: true });
</script>
