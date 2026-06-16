<template>
    <Head title="Payment Checkout" />
    <DealerLayout title="Secure Checkout" eyebrow="Payments">
        <div class="mx-auto max-w-6xl">
            <Link
                :href="actions.wallet"
                class="inline-flex items-center gap-2 text-sm font-semibold text-teal-700 transition hover:text-teal-900"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to wallet
            </Link>

            <section class="mt-5 grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
                <aside class="rounded-lg bg-slate-950 p-5 text-white shadow-sm sm:p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-300">Order summary</p>
                    <h2 class="mt-3 text-2xl font-semibold">{{ typeLabel }}</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-300">
                        Review the amount and choose an available secure payment gateway.
                    </p>

                    <dl class="mt-7 divide-y divide-white/10 rounded-lg bg-white/5 px-4">
                        <div class="flex items-center justify-between gap-4 py-4">
                            <dt class="text-sm font-bold text-slate-300">Payment type</dt>
                            <dd class="text-right text-sm font-semibold">{{ typeLabel }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-4">
                            <dt class="text-sm font-bold text-slate-300">Total payable</dt>
                            <dd class="text-2xl font-semibold text-orange-300">{{ money(amount) }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-start gap-3 rounded-lg border border-white/10 bg-white/5 p-4">
                        <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-teal-300" />
                        <p class="text-sm font-semibold leading-6 text-slate-300">
                            Payment verification is completed by the selected gateway before your account is updated.
                        </p>
                    </div>
                </aside>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Payment method</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Choose how to pay</h2>
                        </div>
                        <LockKeyhole class="h-6 w-6 text-slate-400" />
                    </div>

                    <p v-if="gatewayError" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ gatewayError }}
                    </p>

                    <div class="mt-6 grid gap-4">
                        <button
                            v-if="isRazorpayActive"
                            type="button"
                            class="flex min-h-20 w-full items-center justify-between gap-4 rounded-lg border border-slate-200 px-4 py-4 text-left transition hover:border-teal-300 hover:bg-teal-50 disabled:cursor-wait disabled:opacity-60 sm:px-5"
                            :disabled="processing !== null"
                            @click="payWithRazorpay"
                        >
                            <span class="flex min-w-0 items-center gap-4">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-[#2b4cff] text-white">
                                    <CreditCard class="h-5 w-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-semibold text-slate-950">Razorpay</span>
                                    <span class="mt-1 block text-sm font-semibold text-slate-500">Cards, UPI, net banking and wallets</span>
                                </span>
                            </span>
                            <span class="shrink-0 text-sm font-semibold text-teal-700">
                                {{ processing === 'razorpay' ? 'Opening...' : money(amount) }}
                            </span>
                        </button>

                        <button
                            v-if="isPhonePeActive"
                            type="button"
                            class="flex min-h-20 w-full items-center justify-between gap-4 rounded-lg border border-slate-200 px-4 py-4 text-left transition hover:border-orange-300 hover:bg-orange-50 disabled:cursor-wait disabled:opacity-60 sm:px-5"
                            :disabled="processing !== null"
                            @click="payWithPhonePe"
                        >
                                <span class="flex min-w-0 items-center gap-4">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-[#5f259f] text-white">
                                        <Smartphone class="h-5 w-5" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block font-semibold text-slate-950">PhonePe</span>
                                        <span class="mt-1 block text-sm font-semibold text-slate-500">Continue to PhonePe secure checkout</span>
                                    </span>
                                </span>
                                <span class="shrink-0 text-sm font-semibold text-orange-600">
                                    {{ processing === 'phonepe' ? 'Redirecting...' : money(amount) }}
                                </span>
                        </button>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-bold text-slate-500">
                            <span class="inline-flex items-center gap-1.5"><ShieldCheck class="h-4 w-4 text-teal-600" /> Secure verification</span>
                            <span class="inline-flex items-center gap-1.5"><ReceiptText class="h-4 w-4 text-teal-600" /> Transaction recorded</span>
                            <span class="inline-flex items-center gap-1.5"><IndianRupee class="h-4 w-4 text-teal-600" /> INR payment</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <form ref="razorpayForm" :action="actions.success" method="POST" class="hidden">
            <input type="hidden" name="_token" :value="csrfToken" />
            <input type="hidden" name="razorpay_order_id" :value="order?.order_id || ''" />
            <input type="hidden" name="razorpay_payment_id" :value="razorpayResponse.paymentId" />
            <input type="hidden" name="razorpay_signature" :value="razorpayResponse.signature" />
        </form>
    </DealerLayout>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CreditCard,
    IndianRupee,
    LockKeyhole,
    ReceiptText,
    ShieldCheck,
    Smartphone,
} from '@lucide/vue';
import DealerLayout from '@/Layouts/DealerLayout.vue';
import { startPhonePeCheckout } from '@/Composables/usePhonePeCheckout';

type RazorpayOrder = {
    order_id: string;
    amount: number;
    currency: string;
};

type RazorpayResult = {
    razorpay_payment_id: string;
    razorpay_signature: string;
};

const props = defineProps<{
    order: RazorpayOrder | null;
    type: string;
    amount: number;
    typeLabel: string;
    keyId: string;
    planId: number | null;
    carId: number | null;
    days: number | null;
    isRazorpayActive: boolean;
    isPhonePeActive: boolean;
    paymentIntent: string;
    dealer: { name: string; email: string | null };
    csrfToken: string;
    actions: { success: string; failed: string; phonepe: string; wallet: string };
}>();

const processing = ref<'razorpay' | 'phonepe' | null>(null);
const gatewayError = ref('');
const razorpayForm = ref<HTMLFormElement | null>(null);
const razorpayResponse = reactive({ paymentId: '', signature: '' });

const money = (value: number) =>
    `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const loadRazorpay = () =>
    new Promise<void>((resolve, reject) => {
        if ((window as any).Razorpay) {
            resolve();
            return;
        }

        const existing = document.querySelector<HTMLScriptElement>('script[data-razorpay-checkout]');
        if (existing) {
            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', () => reject(new Error('Unable to load Razorpay checkout.')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.async = true;
        script.dataset.razorpayCheckout = 'true';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Unable to load Razorpay checkout.'));
        document.head.appendChild(script);
    });

const payWithRazorpay = async () => {
    gatewayError.value = '';

    if (!props.order?.order_id || !props.keyId) {
        gatewayError.value = 'Razorpay is not configured correctly. Please choose another payment method.';
        return;
    }

    processing.value = 'razorpay';

    try {
        await loadRazorpay();

        const checkout = new (window as any).Razorpay({
            key: props.keyId,
            amount: Math.round(Number(props.order.amount) * 100),
            currency: props.order.currency || 'INR',
            name: 'SAHI GADI',
            description: props.typeLabel,
            order_id: props.order.order_id,
            prefill: {
                name: props.dealer.name,
                email: props.dealer.email || '',
            },
            theme: { color: '#0f766e' },
            handler: (response: RazorpayResult) => {
                razorpayResponse.paymentId = response.razorpay_payment_id;
                razorpayResponse.signature = response.razorpay_signature;
                requestAnimationFrame(() => razorpayForm.value?.submit());
            },
            modal: {
                ondismiss: () => {
                    processing.value = null;
                },
            },
        });

        checkout.on('payment.failed', () => {
            processing.value = null;
            gatewayError.value = 'Payment was not completed. You can try again or choose PhonePe.';
        });
        checkout.open();
    } catch (error) {
        processing.value = null;
        gatewayError.value = error instanceof Error ? error.message : 'Unable to open Razorpay checkout.';
    }
};

const payWithPhonePe = async () => {
    gatewayError.value = '';
    processing.value = 'phonepe';

    try {
        await startPhonePeCheckout(
            props.actions.phonepe,
            { intent: props.paymentIntent },
            props.csrfToken,
        );
    } catch (error) {
        processing.value = null;
        gatewayError.value = error instanceof Error
            ? error.message
            : 'Unable to open PhonePe checkout.';
    }
};
</script>
