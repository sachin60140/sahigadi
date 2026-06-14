<template>
    <Head :title="`Edit ${customer.name}`" />

    <AdminLayout :title="`Edit ${customer.name}`" eyebrow="Customer operations">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Profile maintenance</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Update {{ customer.name }}.</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ customer.customer_unique_id }} / the customer will receive the existing profile-update email.</p>
                </div>
                <Link :href="actions.back" class="w-fit rounded-lg border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">Back to details</Link>
            </div>

            <div class="mt-5">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-black text-slate-700">Profile completion</p>
                    <p class="text-sm font-black" :class="customer.profile_completion_percentage >= 75 ? 'text-teal-700' : 'text-orange-700'">
                        {{ customer.profile_completion_percentage }}%
                    </p>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div
                        class="h-full rounded-full"
                        :class="customer.profile_completion_percentage >= 75 ? 'bg-teal-600' : 'bg-orange-500'"
                        :style="{ width: `${Math.min(100, customer.profile_completion_percentage)}%` }"
                    ></div>
                </div>
                <div v-if="customer.missing_profile_fields.length" class="mt-3 flex flex-wrap gap-2">
                    <span v-for="field in customer.missing_profile_fields" :key="field" class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-700 ring-1 ring-orange-100">
                        {{ field }}
                    </span>
                </div>
            </div>
        </section>

        <form class="grid gap-5" @submit.prevent="submit">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <SectionHeading eyebrow="Account" title="Primary information" />
                <div class="mt-5 grid gap-4 lg:grid-cols-3">
                    <Field label="Full name" :error="form.errors.name" required>
                        <input v-model="form.name" class="admin-input" required type="text" autocomplete="name" />
                    </Field>
                    <Field label="Email address" :error="form.errors.email" required>
                        <input v-model="form.email" class="admin-input" required type="email" autocomplete="email" />
                    </Field>
                    <Field label="Phone number" :error="form.errors.phone" required>
                        <input v-model="form.phone" class="admin-input" required type="tel" autocomplete="tel" />
                    </Field>
                    <Field label="WhatsApp number" :error="form.errors.whatsapp_number">
                        <input v-model="form.whatsapp_number" class="admin-input" type="tel" />
                    </Field>
                    <Field label="Company name" :error="form.errors.company_name">
                        <input v-model="form.company_name" class="admin-input" type="text" autocomplete="organization" />
                    </Field>
                    <Field label="GST number" :error="form.errors.gst_number">
                        <input v-model="form.gst_number" class="admin-input uppercase" type="text" />
                    </Field>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <SectionHeading eyebrow="Location" title="Address information" />
                <div class="mt-5 grid gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-3">
                        <Field label="Full address" :error="form.errors.address">
                            <textarea v-model="form.address" class="admin-input min-h-28" autocomplete="street-address"></textarea>
                        </Field>
                    </div>
                    <Field label="City" :error="form.errors.city">
                        <input v-model="form.city" class="admin-input" type="text" autocomplete="address-level2" />
                    </Field>
                    <Field label="State" :error="form.errors.state">
                        <input v-model="form.state" class="admin-input" type="text" autocomplete="address-level1" />
                    </Field>
                    <Field label="Pincode" :error="form.errors.pincode">
                        <input v-model="form.pincode" class="admin-input" type="text" autocomplete="postal-code" />
                    </Field>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <SectionHeading eyebrow="Profile completion" title="Identity and personal details" />
                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                    <Field label="Aadhaar number" :error="form.errors.aadhaar_number">
                        <input v-model="form.aadhaar_number" class="admin-input" maxlength="20" type="text" />
                    </Field>
                    <Field label="PAN number" :error="form.errors.pan_number">
                        <input v-model="form.pan_number" class="admin-input uppercase" maxlength="20" type="text" />
                    </Field>
                    <Field label="Gender" :error="form.errors.gender">
                        <select v-model="form.gender" class="admin-input">
                            <option value="">Not specified</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </Field>
                    <Field label="Date of birth" :error="form.errors.dob">
                        <input v-model="form.dob" class="admin-input" :max="today" type="date" />
                    </Field>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Link :href="actions.back" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">Cancel</Link>
                <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-700 disabled:opacity-60" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Changes and Notify' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    customer: {
        customer_unique_id?: string | null;
        name: string;
        email: string;
        phone: string;
        whatsapp_number?: string | null;
        company_name?: string | null;
        gst_number?: string | null;
        address?: string | null;
        city?: string | null;
        state?: string | null;
        pincode?: string | null;
        aadhaar_number?: string | null;
        pan_number?: string | null;
        gender?: string | null;
        dob?: string | null;
        profile_completion_percentage: number;
        missing_profile_fields: string[];
    };
    actions: { update: string; back: string };
}>();

const today = new Date().toISOString().slice(0, 10);
const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    phone: props.customer.phone,
    whatsapp_number: props.customer.whatsapp_number || '',
    company_name: props.customer.company_name || '',
    gst_number: props.customer.gst_number || '',
    address: props.customer.address || '',
    city: props.customer.city || '',
    state: props.customer.state || '',
    pincode: props.customer.pincode || '',
    aadhaar_number: props.customer.aadhaar_number || '',
    pan_number: props.customer.pan_number || '',
    gender: props.customer.gender || '',
    dob: props.customer.dob || '',
});

const submit = () => form.put(props.actions.update, { preserveScroll: true });

const SectionHeading = defineComponent({
    props: { eyebrow: { type: String, required: true }, title: { type: String, required: true } },
    setup(sectionProps) {
        return () => h('div', [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-teal-700' }, sectionProps.eyebrow),
            h('h3', { class: 'mt-1 text-xl font-black text-slate-950' }, sectionProps.title),
        ]);
    },
});

const Field = defineComponent({
    props: {
        label: { type: String, required: true },
        error: { type: String, default: '' },
        required: { type: Boolean, default: false },
    },
    setup(fieldProps, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, [
                fieldProps.label,
                fieldProps.required ? h('span', { class: 'text-red-600' }, ' *') : null,
            ]),
            slots.default?.(),
            fieldProps.error ? h('span', { class: 'mt-2 block text-xs font-bold text-red-700' }, fieldProps.error) : null,
        ]);
    },
});
</script>

<style scoped>
.admin-input {
    min-height: 48px;
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 12px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(30 41 59);
    outline: none;
}
.admin-input:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
