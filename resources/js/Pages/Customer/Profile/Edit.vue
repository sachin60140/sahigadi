<template>
    <Head title="My Profile" />
    <CustomerLayout title="My Profile" eyebrow="Account details">
        <div class="mx-auto max-w-5xl">
            <section class="grid gap-5 lg:grid-cols-[300px_1fr]">
                <aside class="space-y-5">
                    <div class="rounded-lg border border-slate-200 bg-white p-5 text-center shadow-sm">
                        <div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-slate-100 ring-4 ring-white shadow">
                            <img v-if="previewUrl || customer.profile_image_url" :src="previewUrl || customer.profile_image_url" alt="" class="h-full w-full object-cover" />
                            <div v-else class="grid h-full place-items-center text-slate-400"><UserRound class="h-9 w-9" /></div>
                        </div>
                        <h2 class="mt-4 text-lg font-black text-slate-950">{{ customer.name || 'Customer' }}</h2>
                        <p class="mt-1 text-sm font-bold text-slate-500">+91 {{ customer.phone }}</p>
                        <p class="mt-2 text-xs font-black text-teal-700">{{ customer.customer_unique_id }}</p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-black text-slate-950">Profile completion</p>
                            <span class="text-sm font-black" :class="customer.profile_completion_percentage >= 75 ? 'text-teal-700' : 'text-orange-700'">
                                {{ customer.profile_completion_percentage }}%
                            </span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all" :class="customer.profile_completion_percentage >= 75 ? 'bg-teal-600' : 'bg-orange-500'" :style="{ width: `${customer.profile_completion_percentage}%` }"></div>
                        </div>
                        <div v-if="customer.missing_fields.length" class="mt-4">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Still missing</p>
                            <ul class="mt-2 space-y-1.5 text-sm font-semibold text-slate-600">
                                <li v-for="field in customer.missing_fields" :key="field">{{ field }}</li>
                            </ul>
                        </div>
                    </div>
                </aside>

                <form class="rounded-lg border border-slate-200 bg-white shadow-sm" @submit.prevent="submit">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">Personal information</p>
                        <h2 class="mt-1 text-xl font-black text-slate-950">Keep your account up to date</h2>
                    </div>

                    <div class="space-y-7 p-5 sm:p-6">
                        <section>
                            <h3 class="text-sm font-black text-slate-950">Profile and contact</h3>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field label="Profile photo" :error="form.errors.profile_image" class="sm:col-span-2">
                                    <input class="customer-input" type="file" accept="image/jpeg,image/png,image/webp" @change="selectImage" />
                                </Field>
                                <Field label="Full name" :error="form.errors.name"><input v-model="form.name" class="customer-input" required /></Field>
                                <Field label="Email address" :error="form.errors.email"><input v-model="form.email" class="customer-input" type="email" /></Field>
                                <Field label="WhatsApp number" :error="form.errors.whatsapp_number"><input v-model="form.whatsapp_number" class="customer-input" inputmode="tel" /></Field>
                                <Field label="Gender" :error="form.errors.gender">
                                    <select v-model="form.gender" class="customer-input">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </Field>
                                <Field label="Date of birth" :error="form.errors.dob"><input v-model="form.dob" class="customer-input" type="date" /></Field>
                            </div>
                        </section>

                        <section class="border-t border-slate-100 pt-6">
                            <h3 class="text-sm font-black text-slate-950">Identity</h3>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Provide Aadhaar or PAN to complete identity verification.</p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field label="Aadhaar number" :error="form.errors.aadhaar_number"><input v-model="form.aadhaar_number" class="customer-input" /></Field>
                                <Field label="PAN number" :error="form.errors.pan_number"><input v-model="form.pan_number" class="customer-input uppercase" /></Field>
                            </div>
                        </section>

                        <section class="border-t border-slate-100 pt-6">
                            <h3 class="text-sm font-black text-slate-950">Address and business</h3>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field label="Company name" :error="form.errors.company_name"><input v-model="form.company_name" class="customer-input" /></Field>
                                <Field label="GST number" :error="form.errors.gst_number"><input v-model="form.gst_number" class="customer-input uppercase" /></Field>
                                <Field label="Full address" :error="form.errors.address" class="sm:col-span-2"><textarea v-model="form.address" class="customer-input min-h-24" /></Field>
                                <Field label="City" :error="form.errors.city"><input v-model="form.city" class="customer-input" /></Field>
                                <Field label="State" :error="form.errors.state"><input v-model="form.state" class="customer-input" /></Field>
                                <Field label="Pincode" :error="form.errors.pincode"><input v-model="form.pincode" class="customer-input" inputmode="numeric" /></Field>
                            </div>
                        </section>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                        <Link :href="actions.dashboard" class="rounded-lg border border-slate-200 px-5 py-3 text-center text-sm font-black text-slate-700 hover:bg-slate-50">Cancel</Link>
                        <button class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white hover:bg-teal-800 disabled:opacity-50" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save profile' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, onBeforeUnmount, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserRound } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps<{ customer: any; actions: { update: string; dashboard: string } }>();
const form = useForm({
    name: props.customer.name || '',
    email: props.customer.email || '',
    whatsapp_number: props.customer.whatsapp_number || '',
    address: props.customer.address || '',
    city: props.customer.city || '',
    state: props.customer.state || '',
    pincode: props.customer.pincode || '',
    gst_number: props.customer.gst_number || '',
    company_name: props.customer.company_name || '',
    aadhaar_number: props.customer.aadhaar_number || '',
    pan_number: props.customer.pan_number || '',
    gender: props.customer.gender || '',
    dob: props.customer.dob || '',
    profile_image: null as File | null,
});
const previewUrl = ref<string | null>(null);

const selectImage = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.profile_image = file;
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

onBeforeUnmount(() => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
});

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'put' })).post(props.actions.update, {
        forceFormData: true,
        preserveScroll: true,
    });
};

const Field = defineComponent({
    props: { label: { type: String, required: true }, error: { type: String, default: '' } },
    setup(fieldProps, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, fieldProps.label),
            slots.default?.(),
            fieldProps.error ? h('span', { class: 'mt-1 block text-xs font-bold text-red-600' }, fieldProps.error) : null,
        ]);
    },
});
</script>
