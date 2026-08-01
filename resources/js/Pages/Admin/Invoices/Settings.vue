<template>
    <Head title="Invoice Settings" />

    <AdminLayout title="Invoice Settings" eyebrow="GST compliance">
        <Link :href="actions.register" class="inline-flex rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
            Back to invoice register
        </Link>

        <div class="mt-5 rounded-lg border border-orange-100 bg-orange-50 px-4 py-3 text-sm font-medium text-orange-800">
            These values appear on every tax invoice you issue. Confirm the SAC code and GSTIN with your accountant &mdash; an incorrect classification is a compliance issue. Changes apply to invoices issued from now on; previously issued invoices keep the details they were created with.
        </div>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Supplier details</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Who is issuing the invoice</h2>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <Field label="Legal business name" :error="form.errors.invoice_supplier_name">
                        <input v-model="form.invoice_supplier_name" class="admin-input" required />
                    </Field>
                    <Field label="GSTIN" :error="form.errors.invoice_supplier_gstin">
                        <input v-model="form.invoice_supplier_gstin" class="admin-input uppercase" maxlength="20" />
                        <p class="mt-2 text-xs font-medium text-slate-500">First two digits are your state code &mdash; currently <strong>{{ meta.supplier_state_code || 'unknown' }}</strong>.</p>
                    </Field>
                    <Field class="md:col-span-2" label="Registered address" :error="form.errors.invoice_supplier_address">
                        <textarea v-model="form.invoice_supplier_address" class="admin-input min-h-[80px]" required></textarea>
                    </Field>
                    <Field label="State (place of supply origin)" :error="form.errors.invoice_supplier_state">
                        <input v-model="form.invoice_supplier_state" class="admin-input" list="state-list" required />
                        <datalist id="state-list">
                            <option v-for="state in stateNames" :key="state" :value="state" />
                        </datalist>
                        <p class="mt-2 text-xs font-medium text-slate-500">Buyers in this state are billed CGST + SGST; all others IGST.</p>
                    </Field>
                    <Field label="SAC code" :error="form.errors.invoice_sac_code">
                        <input v-model="form.invoice_sac_code" class="admin-input" required />
                        <p class="mt-2 text-xs font-medium text-slate-500">Service accounting code for the supply. Confirm with your CA.</p>
                    </Field>
                </div>

                <p class="mt-8 text-xs font-semibold uppercase tracking-wide text-teal-700">Numbering and tax</p>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <Field label="Invoice number prefix" :error="form.errors.invoice_prefix">
                        <input v-model="form.invoice_prefix" class="admin-input" required />
                        <p class="mt-2 text-xs font-medium text-slate-500">Series resets each financial year (April&ndash;March).</p>
                    </Field>
                    <Field label="GST rate (%)" :error="form.errors.invoice_gst_rate">
                        <input v-model="form.invoice_gst_rate" class="admin-input" type="number" min="0" max="100" step="0.01" required />
                        <p class="mt-2 text-xs font-medium text-slate-500">Split in half for CGST/SGST on intra-state supplies.</p>
                    </Field>
                </div>

                <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                    <button type="submit" class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:opacity-60" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save invoice settings' }}
                    </button>
                </div>
            </form>

            <aside class="self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Preview</p>
                <h3 class="mt-2 text-lg font-bold text-slate-950">Next invoice number</h3>
                <p class="mt-2 rounded-lg bg-slate-50 px-3 py-3 font-mono text-sm font-semibold text-slate-800">{{ meta.next_number }}</p>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">How tax is applied</p>
                    <ul class="mt-3 grid gap-2 text-sm font-medium text-slate-600">
                        <li><strong class="text-slate-900">Same state</strong> as supplier &rarr; CGST {{ half }}% + SGST {{ half }}%</li>
                        <li><strong class="text-slate-900">Different state</strong> &rarr; IGST {{ form.invoice_gst_rate }}%</li>
                        <li>Place of supply comes from the buyer's saved state.</li>
                    </ul>
                </div>

                <p class="mt-5 text-xs font-medium text-slate-500">
                    Invoices are issued automatically on successful wallet recharges through Razorpay and PhonePe.
                </p>
            </aside>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    settings: Record<string, any>;
    meta: { supplier_state_code: string | null; next_number: string; states: string[] };
    actions: { update: string; register: string };
}>();

const form = useForm({ ...props.settings });

const half = computed(() => {
    const rate = Number(form.invoice_gst_rate || 0) / 2;
    return Number.isInteger(rate) ? rate : rate.toFixed(2);
});

// State names for the datalist, derived from the model's code map on the server.
const stateNames = [
    'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar',
    'Chandigarh', 'Chhattisgarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Goa',
    'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jammu and Kashmir', 'Jharkhand', 'Karnataka',
    'Kerala', 'Ladakh', 'Lakshadweep', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya',
    'Mizoram', 'Nagaland', 'Odisha', 'Puducherry', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
    'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
];

const submit = () => form.post(props.actions.update, { preserveScroll: true });

const Field = defineComponent({
    props: { label: { type: String, required: true }, error: { type: String, default: '' } },
    setup(p, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, p.label),
            slots.default?.(),
            p.error ? h('p', { class: 'mt-2 text-xs font-semibold text-red-600' }, p.error) : null,
        ]);
    },
});
</script>
