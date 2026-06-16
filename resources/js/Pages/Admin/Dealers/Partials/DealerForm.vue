<template>
    <form class="grid gap-5" @submit.prevent="$emit('submit')">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <SectionHeading eyebrow="Account" title="Basic information" />
            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                <Field label="Dealer name" :error="form.errors.name" required>
                    <input v-model="form.name" class="admin-input" required type="text" autocomplete="name" />
                </Field>
                <Field label="Email address" :error="form.errors.email" required>
                    <input v-model="form.email" class="admin-input" required type="email" autocomplete="email" />
                </Field>
                <Field label="Phone number" :error="form.errors.phone">
                    <input v-model="form.phone" class="admin-input" type="tel" autocomplete="tel" />
                </Field>
                <Field label="Company name" :error="form.errors.company_name">
                    <input v-model="form.company_name" class="admin-input" type="text" autocomplete="organization" />
                </Field>
                <Field :label="isEdit ? 'New password' : 'Password'" :error="form.errors.password" :required="!isEdit">
                    <input
                        v-model="form.password"
                        class="admin-input"
                        :required="!isEdit"
                        type="password"
                        autocomplete="new-password"
                        :placeholder="isEdit ? 'Leave blank to keep current password' : 'Minimum 6 characters'"
                    />
                </Field>
                <Field label="Account status" :error="form.errors.status" required>
                    <select v-model="form.status" class="admin-input" required>
                        <option value="pending">Pending review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </Field>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <SectionHeading eyebrow="Showroom" title="Address and location" />
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
            <SectionHeading eyebrow="Verification" title="Identity documents" />
            <div class="mt-5 grid gap-6 xl:grid-cols-2">
                <DocumentPanel title="Primary KYC document" :current-url="documents.kyc">
                    <div class="grid gap-4">
                        <Field label="Document type" :error="form.errors.kyc_document_type">
                            <select v-model="form.kyc_document_type" class="admin-input">
                                <option value="">Select type</option>
                                <option value="aadhar">Aadhaar Card</option>
                                <option value="pan">PAN Card</option>
                                <option value="voter_id">Voter ID</option>
                                <option value="driving_license">Driving License</option>
                            </select>
                        </Field>
                        <Field label="Document number" :error="form.errors.kyc_document_number">
                            <input v-model="form.kyc_document_number" class="admin-input" type="text" />
                        </Field>
                        <Field label="Upload document" :error="form.errors.kyc_document">
                            <input class="admin-file" accept=".pdf,.jpg,.jpeg,.png" type="file" @change="selectFile('kyc_document', $event)" />
                        </Field>
                    </div>
                </DocumentPanel>

                <DocumentPanel title="PAN document" :current-url="documents.pan">
                    <div class="grid gap-4">
                        <Field label="PAN number" :error="form.errors.pan_number">
                            <input v-model="form.pan_number" class="admin-input uppercase" maxlength="20" type="text" />
                        </Field>
                        <Field label="Upload PAN document" :error="form.errors.pan_document">
                            <input class="admin-file" accept=".pdf,.jpg,.jpeg,.png" type="file" @change="selectFile('pan_document', $event)" />
                        </Field>
                    </div>
                </DocumentPanel>
            </div>
            <p class="mt-4 text-xs font-bold text-slate-500">Accepted formats: PDF, JPG and PNG up to 5 MB per document.</p>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <SectionHeading eyebrow="Tax profile" title="GST information" />
            <div class="mt-5 grid gap-5 lg:grid-cols-2">
                <Field label="GST number" :error="form.errors.gst_number">
                    <input v-model="form.gst_number" class="admin-input uppercase" maxlength="15" type="text" />
                </Field>
                <DocumentPanel title="GST document" :current-url="documents.gst" compact>
                    <Field label="Upload GST document" :error="form.errors.gst_document">
                        <input class="admin-file" accept=".pdf,.jpg,.jpeg,.png" type="file" @change="selectFile('gst_document', $event)" />
                    </Field>
                </DocumentPanel>
            </div>
        </section>

        <section v-if="!isEdit" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <SectionHeading eyebrow="Subscription" title="Initial plan assignment" />
            <div class="mt-5 max-w-2xl">
                <Field label="Dealer plan" :error="form.errors.assign_plan">
                    <select v-model="form.assign_plan" class="admin-input">
                        <option value="">Create without a plan</option>
                        <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                            {{ plan.name }} - {{ formatCurrency(plan.price) }} / {{ plan.listing_limit }} listings / {{ plan.duration_days }} days
                        </option>
                    </select>
                    <p class="mt-2 text-xs font-bold text-slate-500">Selecting a plan activates it immediately after the dealer account is created.</p>
                </Field>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link :href="cancelUrl" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </Link>
            <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:opacity-60" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

type Plan = { id: number; name: string; price: number; listing_limit: number; duration_days: number };

const props = defineProps<{
    form: any;
    plans: Plan[];
    documents?: { kyc?: string | null; pan?: string | null; gst?: string | null };
    cancelUrl: string;
    submitLabel: string;
    isEdit?: boolean;
}>();

defineEmits<{ submit: [] }>();

const documents = props.documents || {};
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const selectFile = (field: 'kyc_document' | 'pan_document' | 'gst_document', event: Event) => {
    props.form[field] = (event.target as HTMLInputElement).files?.[0] || null;
};

const SectionHeading = defineComponent({
    props: { eyebrow: { type: String, required: true }, title: { type: String, required: true } },
    setup(sectionProps) {
        return () => h('div', [
            h('p', { class: 'text-xs font-semibold uppercase tracking-wide text-teal-700' }, sectionProps.eyebrow),
            h('h3', { class: 'mt-1 text-xl font-semibold text-slate-950' }, sectionProps.title),
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
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, [
                fieldProps.label,
                fieldProps.required ? h('span', { class: 'text-red-600' }, ' *') : null,
            ]),
            slots.default?.(),
            fieldProps.error ? h('span', { class: 'mt-2 block text-xs font-bold text-red-700' }, fieldProps.error) : null,
        ]);
    },
});

const DocumentPanel = defineComponent({
    props: {
        title: { type: String, required: true },
        currentUrl: { type: String, default: '' },
        compact: { type: Boolean, default: false },
    },
    setup(panelProps, { slots }) {
        return () => h('div', { class: panelProps.compact ? '' : 'border-l-2 border-slate-200 pl-4 sm:pl-5' }, [
            h('div', { class: 'mb-4 flex flex-wrap items-center justify-between gap-2' }, [
                h('h4', { class: 'text-sm font-semibold text-slate-950' }, panelProps.title),
                panelProps.currentUrl
                    ? h('a', {
                        href: panelProps.currentUrl,
                        target: '_blank',
                        rel: 'noreferrer',
                        class: 'rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-700',
                    }, 'View current')
                    : h('span', { class: 'text-xs font-bold text-slate-400' }, 'Not uploaded'),
            ]),
            slots.default?.(),
        ]);
    },
});
</script>

<style scoped>
.admin-input,
.admin-file {
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
.admin-input:focus,
.admin-file:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
