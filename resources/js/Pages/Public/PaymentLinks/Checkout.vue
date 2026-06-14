<template>
    <Head>
        <title>Secure Payment</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <PublicLayout>
        <section class="bg-[linear-gradient(135deg,#f6fbff_0%,#edf9f5_55%,#fff8ef_100%)] px-4 py-10 sm:py-16">
            <div class="mx-auto max-w-xl">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70 sm:p-8">
                    <div class="text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-teal-50 text-teal-700">
                            <ShieldCheck class="h-7 w-7" />
                        </span>
                        <p class="mt-5 text-xs font-black uppercase tracking-wide text-orange-600">SahiGadi payment link</p>
                        <h1 class="mt-2 text-3xl font-black text-slate-950">Secure checkout</h1>
                        <p class="mt-3 text-sm font-semibold text-slate-600">Choose an available payment method to complete this request.</p>
                    </div>

                    <dl class="mt-7 rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="text-center">
                            <dt class="text-xs font-black uppercase tracking-wide text-slate-500">Amount to pay</dt>
                            <dd class="mt-2 text-4xl font-black text-slate-950">{{ money(link.amount) }}</dd>
                        </div>
                        <div class="mt-5 grid gap-3 border-t border-slate-200 pt-5 text-sm">
                            <div class="flex items-start justify-between gap-4">
                                <dt class="font-bold text-slate-500">Purpose</dt>
                                <dd class="max-w-[65%] text-right font-black text-slate-900">{{ link.purpose }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <dt class="font-bold text-slate-500">Payee</dt>
                                <dd class="max-w-[65%] text-right font-black text-slate-900">{{ link.payee || 'SahiGadi' }}</dd>
                            </div>
                            <div v-if="link.expires_at" class="flex items-start justify-between gap-4">
                                <dt class="font-bold text-slate-500">Valid until</dt>
                                <dd class="text-right font-black text-slate-900">{{ formatDate(link.expires_at) }}</dd>
                            </div>
                        </div>
                    </dl>

                    <div v-if="flashError || errorMessage" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ flashError || errorMessage }}
                    </div>

                    <div class="mt-6 grid gap-3">
                        <button
                            v-if="phonePeAvailable"
                            type="button"
                            class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-[#5f259f] px-5 py-3 text-sm font-black text-white transition hover:bg-[#4f1f84] disabled:opacity-60"
                            :disabled="processing !== null"
                            @click="submitPhonePe"
                        >
                            <LoaderCircle v-if="processing === 'phonepe'" class="h-5 w-5 animate-spin" />
                            <Smartphone v-else class="h-5 w-5" />
                            {{ processing === 'phonepe' ? 'Opening PhonePe...' : 'Pay with PhonePe' }}
                        </button>

                        <button
                            v-if="razorpayAvailable"
                            type="button"
                            class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-[#2563eb] px-5 py-3 text-sm font-black text-white transition hover:bg-[#1d4ed8] disabled:opacity-60"
                            :disabled="processing !== null"
                            @click="openRazorpay"
                        >
                            <LoaderCircle v-if="processing === 'razorpay'" class="h-5 w-5 animate-spin" />
                            <CreditCard v-else class="h-5 w-5" />
                            {{ processing === 'razorpay' ? 'Opening Razorpay...' : 'Pay with Razorpay' }}
                        </button>

                        <div v-if="!phonePeAvailable && !razorpayAvailable" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-center text-sm font-bold text-amber-800">
                            Payments are currently unavailable. Please contact the payment-link issuer.
                        </div>
                    </div>

                    <p class="mt-5 flex items-center justify-center gap-2 text-center text-xs font-bold text-slate-500">
                        <LockKeyhole class="h-4 w-4 text-teal-700" />
                        Payment details are handled by the selected secure gateway.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { CreditCard, LoaderCircle, LockKeyhole, ShieldCheck, Smartphone } from '@lucide/vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { startPhonePeCheckout } from '@/Composables/usePhonePeCheckout';

declare global {
    interface Window {
        Razorpay?: new (options: Record<string, unknown>) => {
            open: () => void;
            on: (event: string, callback: (response: any) => void) => void;
        };
    }
}

const props = defineProps<{
    link: {
        id: string;
        amount: number;
        purpose: string;
        gateway: string;
        payee: string | null;
        customer: { name?: string | null; email?: string | null; phone?: string | null };
        expires_at?: string | null;
    };
    order?: { order_id: string; amount: number } | null;
    keyId: string;
    isRazorpayActive: boolean;
    isPhonePeActive: boolean;
    checkoutUrl: string;
}>();

const page = usePage();
const processing = ref<'phonepe' | 'razorpay' | null>(null);
const errorMessage = ref('');
const scriptReady = ref(false);
const flashError = computed(() => (page.props as any).flash?.error || '');
const phonePeAvailable = computed(() => props.isPhonePeActive && ['any', 'phonepe'].includes(props.link.gateway));
const razorpayAvailable = computed(() => props.isRazorpayActive && ['any', 'razorpay'].includes(props.link.gateway) && Boolean(props.order));
const csrf = () => document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const formatDate = (value: string) => new Intl.DateTimeFormat('en-IN', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));

const submitNative = (values: Record<string, string>) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.checkoutUrl;
    Object.entries({ _token: csrf(), ...values }).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
};

const loadRazorpay = () => new Promise<void>((resolve, reject) => {
    if (window.Razorpay) {
        scriptReady.value = true;
        resolve();
        return;
    }
    const script = document.createElement('script');
    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
    script.async = true;
    script.onload = () => {
        scriptReady.value = true;
        resolve();
    };
    script.onerror = () => reject(new Error('Razorpay checkout could not be loaded.'));
    document.head.appendChild(script);
});

const submitPhonePe = async () => {
    processing.value = 'phonepe';
    errorMessage.value = '';

    try {
        await startPhonePeCheckout(
            props.checkoutUrl,
            { gateway: 'phonepe' },
            csrf(),
        );
    } catch (error) {
        processing.value = null;
        errorMessage.value = error instanceof Error
            ? error.message
            : 'Unable to open PhonePe checkout.';
    }
};

const openRazorpay = async () => {
    processing.value = 'razorpay';
    errorMessage.value = '';
    try {
        if (!scriptReady.value) await loadRazorpay();
        if (!window.Razorpay || !props.order) throw new Error('Razorpay checkout is unavailable.');
        const checkout = new window.Razorpay({
            key: props.keyId,
            amount: Math.round(Number(props.order.amount) * 100),
            currency: 'INR',
            name: 'SAHI GADI',
            description: props.link.purpose,
            order_id: props.order.order_id,
            prefill: {
                name: props.link.customer.name || '',
                email: props.link.customer.email || '',
                contact: props.link.customer.phone || '',
            },
            theme: { color: '#2563eb' },
            handler: (response: any) => submitNative({
                gateway: 'razorpay',
                razorpay_order_id: response.razorpay_order_id || props.order?.order_id || '',
                razorpay_payment_id: response.razorpay_payment_id || '',
                razorpay_signature: response.razorpay_signature || '',
            }),
            modal: { ondismiss: () => { processing.value = null; } },
        });
        checkout.on('payment.failed', (response: any) => {
            errorMessage.value = response?.error?.description || 'Payment failed. Please try again.';
            processing.value = null;
        });
        checkout.open();
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Razorpay checkout could not be opened.';
        processing.value = null;
    }
};

onMounted(() => {
    if (razorpayAvailable.value) loadRazorpay().catch(() => {
        errorMessage.value = 'Razorpay checkout could not be loaded. Check your connection and try again.';
    });
});
</script>
