<template>
    <Head>
        <title>Complete Payment</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <PublicLayout>
        <section class="bg-[linear-gradient(135deg,#f6fbff_0%,#edf9f5_55%,#fff8ef_100%)] px-4 py-10 sm:py-16">
            <div class="mx-auto max-w-xl">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70 sm:p-8">
                    <div class="text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-teal-50 text-teal-700">
                            <CreditCard class="h-7 w-7" />
                        </span>
                        <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-orange-600">Secure report payment</p>
                        <h1 class="mt-2 text-3xl font-semibold text-slate-950">Complete payment</h1>
                        <p class="mt-3 text-sm font-semibold text-slate-600">Vehicle <span class="font-semibold uppercase text-slate-950">{{ vehicleNumber }}</span></p>
                    </div>

                    <div class="mt-7 rounded-lg border border-slate-200 bg-slate-50 p-5 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ reportLabel }}</p>
                        <p class="mt-2 text-4xl font-semibold text-slate-950">{{ money(amount) }}</p>
                    </div>

                    <div v-if="errorMessage" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ errorMessage }}
                    </div>

                    <button
                        type="button"
                        class="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="loading"
                        @click="openCheckout"
                    >
                        <LoaderCircle v-if="loading" class="h-5 w-5 animate-spin" />
                        <LockKeyhole v-else class="h-5 w-5" />
                        {{ loading ? 'Opening secure checkout...' : `Pay ${money(amount)}` }}
                    </button>

                    <div class="mt-5 flex items-center justify-center gap-2 text-xs font-bold text-slate-500">
                        <ShieldCheck class="h-4 w-4 text-teal-700" />
                        Secure payment via Razorpay
                    </div>

                    <Link :href="cancelUrl" class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-950">
                        <ArrowLeft class="h-4 w-4" />
                        Return to search
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard, LoaderCircle, LockKeyhole, ShieldCheck } from '@lucide/vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

declare global {
    interface Window {
        Razorpay?: new (options: Record<string, unknown>) => {
            open: () => void;
            on: (event: string, callback: (response: any) => void) => void;
        };
    }
}

const props = defineProps<{
    orderId: string;
    amount: number;
    keyId: string;
    vehicleNumber: string;
    customerName: string;
    reportLabel: string;
    description: string;
    callbackUrl: string;
    cancelUrl: string;
}>();

const loading = ref(false);
const errorMessage = ref('');
const scriptReady = ref(false);

const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0))}`;
const csrf = () => document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';

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
    script.onerror = () => reject(new Error('Secure checkout could not be loaded. Please try again.'));
    document.head.appendChild(script);
});

const submitCallback = (response: any) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.callbackUrl;
    const values = {
        _token: csrf(),
        razorpay_order_id: response.razorpay_order_id || props.orderId,
        razorpay_payment_id: response.razorpay_payment_id || '',
        razorpay_signature: response.razorpay_signature || '',
    };

    Object.entries(values).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
};

const openCheckout = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        if (!scriptReady.value) await loadRazorpay();
        if (!window.Razorpay) throw new Error('Secure checkout is unavailable.');

        const checkout = new window.Razorpay({
            key: props.keyId,
            amount: Math.round(Number(props.amount) * 100),
            currency: 'INR',
            name: 'SAHI GADI',
            description: props.description,
            order_id: props.orderId,
            prefill: { name: props.customerName },
            theme: { color: '#f97316' },
            handler: submitCallback,
            modal: { ondismiss: () => { loading.value = false; } },
        });
        checkout.on('payment.failed', (response: any) => {
            errorMessage.value = response?.error?.description || 'Payment failed. Please try again.';
            loading.value = false;
        });
        checkout.open();
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Secure checkout could not be opened.';
        loading.value = false;
    }
};

onMounted(() => {
    loadRazorpay().catch(() => {
        errorMessage.value = 'Secure checkout could not be loaded. Check your connection and try again.';
    });
});
</script>
