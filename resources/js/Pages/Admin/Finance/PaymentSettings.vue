<template>
    <Head title="Payment Settings" />

    <AdminLayout title="Payment Settings" eyebrow="Gateway control">
        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">Gateway configuration</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Razorpay and PhonePe setup</h2>
                        <p class="mt-2 max-w-2xl text-sm font-semibold leading-7 text-slate-600">
                            Control active gateways, credentials, environment and minimum wallet recharge rules.
                        </p>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-fit"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Settings' }}
                    </button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <ToggleCard
                        label="Enable Razorpay"
                        text="Allow Razorpay checkout and refund workflows."
                        v-model="form.is_razorpay_active"
                    />
                    <ToggleCard
                        label="Enable PhonePe"
                        text="Allow PhonePe checkout, payment links and webhook sync."
                        v-model="form.is_phonepe_active"
                    />
                </div>

                <SectionTitle title="Razorpay Settings" />
                <div class="grid gap-4 md:grid-cols-2">
                    <Field label="Razorpay Key ID" error-key="razorpay_key_id">
                        <input v-model="form.razorpay_key_id" class="admin-input" type="text" required placeholder="rzp_test_xxxxx" />
                    </Field>
                    <Field label="Razorpay Key Secret" error-key="razorpay_key_secret">
                        <input v-model="form.razorpay_key_secret" class="admin-input" type="password" required placeholder="Secret key" />
                    </Field>
                </div>

                <SectionTitle title="PhonePe Settings" />
                <div class="grid gap-4 md:grid-cols-2">
                    <Field label="PhonePe Client ID" error-key="phonepe_merchant_id">
                        <input v-model="form.phonepe_merchant_id" class="admin-input" type="text" required />
                    </Field>
                    <Field label="PhonePe Client Secret" error-key="phonepe_salt_key">
                        <input v-model="form.phonepe_salt_key" class="admin-input" type="password" required />
                    </Field>
                    <Field label="PhonePe Client Version" error-key="phonepe_salt_index">
                        <input v-model="form.phonepe_salt_index" class="admin-input" type="text" required />
                    </Field>
                    <Field label="Environment" error-key="phonepe_env">
                        <select v-model="form.phonepe_env" class="admin-input" required>
                            <option value="UAT">UAT (Testing)</option>
                            <option value="PRODUCTION">Production</option>
                        </select>
                    </Field>
                    <Field label="Custom Checkout API URL" error-key="phonepe_checkout_url" wrapper-class="md:col-span-2">
                        <input v-model="form.phonepe_checkout_url" class="admin-input" type="url" placeholder="https://api.phonepe.com/apis/pg/checkout/v2/pay" />
                    </Field>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-200 bg-teal-50 px-5 py-3 text-sm font-black text-teal-800 transition hover:bg-teal-100 disabled:cursor-wait disabled:opacity-60"
                        :disabled="testingPhonePe || form.processing"
                        @click="testPhonePe"
                    >
                        {{ testingPhonePe ? 'Testing PhonePe...' : 'Test PhonePe Connection' }}
                    </button>
                    <p class="text-sm font-semibold text-slate-500">Save credentials first, then test authentication without creating a payment.</p>
                </div>

                <SectionTitle title="Recharge Rules" />
                <div class="grid gap-4 md:grid-cols-2">
                    <Field label="Dealer Minimum Recharge" error-key="min_wallet_recharge_amount">
                        <input v-model="form.min_wallet_recharge_amount" class="admin-input" min="1" step="0.01" type="number" required />
                    </Field>
                    <Field label="Customer Minimum Recharge" error-key="customer_min_wallet_recharge_amount">
                        <input v-model="form.customer_min_wallet_recharge_amount" class="admin-input" min="1" step="0.01" type="number" required />
                    </Field>
                </div>
            </form>

            <aside class="grid content-start gap-4">
                <InfoPanel
                    title="Gateway policy"
                    text="Keep at least one gateway enabled when payment links or wallet recharge flows are active."
                    tone="teal"
                />
                <InfoPanel
                    title="Production care"
                    text="Switch PhonePe to production only after credentials and callback URLs have been verified."
                    tone="orange"
                />
                <InfoPanel
                    title="Sensitive fields"
                    text="Secrets are posted back only when you save this screen. Avoid sharing screenshots of live credentials."
                    tone="slate"
                />
            </aside>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, ref, useSlots } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type Settings = {
    razorpay_key_id: string;
    razorpay_key_secret: string;
    min_wallet_recharge_amount: number | string;
    customer_min_wallet_recharge_amount: number | string;
    phonepe_merchant_id: string;
    phonepe_salt_key: string;
    phonepe_salt_index: string;
    phonepe_env: string;
    phonepe_checkout_url?: string | null;
    is_razorpay_active: boolean;
    is_phonepe_active: boolean;
};

const props = defineProps<{ settings: Settings }>();

const form = useForm({ ...props.settings });
const testingPhonePe = ref(false);

const submit = () => {
    form.post('/admin/payment-settings', {
        preserveScroll: true,
    });
};

const testPhonePe = () => {
    testingPhonePe.value = true;
    router.post('/admin/payment-settings/test-phonepe', {}, {
        preserveScroll: true,
        onFinish: () => {
            testingPhonePe.value = false;
        },
    });
};

const Field = defineComponent({
    props: {
        label: { type: String, required: true },
        errorKey: { type: String, required: true },
        wrapperClass: { type: String, default: '' },
    },
    setup(fieldProps) {
        const slots = useSlots();
        return () => h('label', { class: ['block', fieldProps.wrapperClass] }, [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, fieldProps.label),
            slots.default?.(),
            form.errors[fieldProps.errorKey]
                ? h('span', { class: 'mt-2 block text-xs font-bold text-red-600' }, form.errors[fieldProps.errorKey])
                : null,
        ]);
    },
});

const SectionTitle = defineComponent({
    props: { title: { type: String, required: true } },
    setup(sectionProps) {
        return () => h('div', { class: 'mb-4 mt-8 border-b border-slate-200 pb-3' }, [
            h('h3', { class: 'text-lg font-black text-slate-950' }, sectionProps.title),
        ]);
    },
});

const ToggleCard = defineComponent({
    props: {
        modelValue: { type: Boolean, required: true },
        label: { type: String, required: true },
        text: { type: String, required: true },
    },
    emits: ['update:modelValue'],
    setup(toggleProps, { emit }) {
        return () => h('button', {
            type: 'button',
            class: [
                'rounded-lg border p-4 text-left transition',
                toggleProps.modelValue ? 'border-teal-200 bg-teal-50' : 'border-slate-200 bg-slate-50 hover:bg-white',
            ],
            onClick: () => emit('update:modelValue', !toggleProps.modelValue),
        }, [
            h('div', { class: 'flex items-center justify-between gap-3' }, [
                h('div', [
                    h('p', { class: 'text-sm font-black text-slate-950' }, toggleProps.label),
                    h('p', { class: 'mt-1 text-sm font-semibold leading-6 text-slate-600' }, toggleProps.text),
                ]),
                h('span', {
                    class: [
                        'relative h-6 w-11 rounded-full transition',
                        toggleProps.modelValue ? 'bg-teal-700' : 'bg-slate-300',
                    ],
                }, [
                    h('span', {
                        class: [
                            'absolute top-1 h-4 w-4 rounded-full bg-white transition',
                            toggleProps.modelValue ? 'left-6' : 'left-1',
                        ],
                    }),
                ]),
            ]),
        ]);
    },
});

const InfoPanel = defineComponent({
    props: {
        title: { type: String, required: true },
        text: { type: String, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(panelProps) {
        const toneClass = {
            teal: 'border-teal-100 bg-teal-50 text-teal-800',
            orange: 'border-orange-100 bg-orange-50 text-orange-800',
            slate: 'border-slate-200 bg-white text-slate-700',
        }[panelProps.tone] || 'border-slate-200 bg-white text-slate-700';

        return () => h('div', { class: ['rounded-lg border p-5 shadow-sm', toneClass] }, [
            h('h3', { class: 'text-base font-black text-slate-950' }, panelProps.title),
            h('p', { class: 'mt-2 text-sm font-semibold leading-6' }, panelProps.text),
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
    transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}
.admin-input:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
