<template>
    <Head title="Payment Checkout" />
    <CustomerLayout title="Secure Checkout" eyebrow="Wallet recharge">
        <div class="mx-auto max-w-5xl">
            <Link :href="actions.wallet" class="inline-flex items-center gap-2 text-sm font-black text-teal-700 hover:text-teal-900"><ArrowLeft class="h-4 w-4" /> Back to wallet</Link>

            <section class="mt-5 grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
                <aside class="rounded-lg bg-slate-950 p-5 text-white shadow-sm sm:p-6">
                    <p class="text-xs font-black uppercase tracking-wide text-teal-300">Order summary</p>
                    <h2 class="mt-3 text-2xl font-black">{{ typeLabel }}</h2>
                    <dl class="mt-7 divide-y divide-white/10 rounded-lg bg-white/5 px-4">
                        <Row label="Wallet credit" :value="money(baseAmount)" />
                        <Row label="GST (18%)" :value="money(amount - baseAmount)" />
                        <Row label="Total payable" :value="money(amount)" strong />
                    </dl>
                    <div class="mt-6 flex items-start gap-3 rounded-lg border border-white/10 bg-white/5 p-4">
                        <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-teal-300" />
                        <p class="text-sm font-semibold leading-6 text-slate-300">Your wallet is credited only after gateway verification succeeds.</p>
                    </div>
                </aside>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Payment method</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950">Choose how to pay</h2>
                    <p v-if="gatewayError" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ gatewayError }}</p>

                    <div class="mt-6 grid gap-4">
                        <button v-if="isRazorpayActive" type="button" class="flex min-h-20 items-center justify-between gap-4 rounded-lg border border-slate-200 px-4 py-4 text-left hover:border-teal-300 hover:bg-teal-50 disabled:opacity-60" :disabled="processing !== null" @click="payWithRazorpay">
                            <span class="flex min-w-0 items-center gap-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-[#2b4cff] text-white"><CreditCard class="h-5 w-5" /></span><span><span class="block font-black text-slate-950">Razorpay</span><span class="mt-1 block text-sm font-semibold text-slate-500">Cards, UPI, net banking and wallets</span></span></span>
                            <span class="shrink-0 text-sm font-black text-teal-700">{{ processing === 'razorpay' ? 'Opening...' : money(amount) }}</span>
                        </button>
                        <form v-if="isPhonePeActive" :action="actions.phonepe" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken" />
                            <input type="hidden" name="intent" :value="paymentIntent" />
                            <button type="submit" class="flex min-h-20 w-full items-center justify-between gap-4 rounded-lg border border-slate-200 px-4 py-4 text-left hover:border-orange-300 hover:bg-orange-50 disabled:opacity-60" :disabled="processing !== null" @click="processing = 'phonepe'">
                                <span class="flex min-w-0 items-center gap-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-[#5f259f] text-white"><Smartphone class="h-5 w-5" /></span><span><span class="block font-black text-slate-950">PhonePe</span><span class="mt-1 block text-sm font-semibold text-slate-500">Continue to secure checkout</span></span></span>
                                <span class="shrink-0 text-sm font-black text-orange-600">{{ processing === 'phonepe' ? 'Redirecting...' : money(amount) }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <form ref="razorpayForm" :action="actions.success" method="POST" class="hidden">
            <input type="hidden" name="_token" :value="csrfToken" />
            <input type="hidden" name="razorpay_order_id" :value="order?.order_id || ''" />
            <input type="hidden" name="razorpay_payment_id" :value="response.paymentId" />
            <input type="hidden" name="razorpay_signature" :value="response.signature" />
        </form>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard, ShieldCheck, Smartphone } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps<{ order: any; type: string; amount: number; baseAmount: number; typeLabel: string; keyId: string; isRazorpayActive: boolean; isPhonePeActive: boolean; paymentIntent: string; customer: { name: string; email: string | null }; csrfToken: string; actions: { success: string; phonepe: string; wallet: string } }>();
const processing = ref<'razorpay' | 'phonepe' | null>(null);
const gatewayError = ref('');
const razorpayForm = ref<HTMLFormElement | null>(null);
const response = reactive({ paymentId: '', signature: '' });
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const loadRazorpay = () => new Promise<void>((resolve, reject) => {
    if ((window as any).Razorpay) return resolve();
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
        gatewayError.value = 'Razorpay is not configured correctly. Please choose another method.';
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
            prefill: { name: props.customer.name, email: props.customer.email || '' },
            theme: { color: '#0f766e' },
            handler: (result: any) => {
                response.paymentId = result.razorpay_payment_id;
                response.signature = result.razorpay_signature;
                requestAnimationFrame(() => razorpayForm.value?.submit());
            },
            modal: { ondismiss: () => { processing.value = null; } },
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

const Row = defineComponent({ props: { label: { type: String, required: true }, value: { type: String, required: true }, strong: { type: Boolean, default: false } }, setup(p) { return () => h('div', { class: 'flex items-center justify-between gap-4 py-4' }, [h('dt', { class: p.strong ? 'font-black text-white' : 'text-sm font-bold text-slate-300' }, p.label), h('dd', { class: p.strong ? 'text-2xl font-black text-orange-300' : 'text-sm font-black text-white' }, p.value)]); } });
</script>
