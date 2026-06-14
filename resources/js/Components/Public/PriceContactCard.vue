<template>
    <aside class="rounded-lg border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/80 lg:sticky lg:top-24 xl:p-6">
        <div class="border-b border-slate-100 pb-5">
            <p class="text-xs font-black uppercase tracking-wide text-teal-700">Seller access</p>
            <h2 class="mt-2 text-xl font-black leading-tight tracking-normal text-slate-950">Price and contact</h2>
            <p class="mt-2 text-sm font-semibold text-slate-500">{{ variant || model || 'Used car listing' }}</p>
            <p class="mt-3 flex items-center gap-1.5 text-sm font-bold text-slate-500">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ city || 'Bihar' }}<span v-if="state">, {{ state }}</span>
            </p>
        </div>

        <div class="border-b border-slate-100 py-5">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Asking Price</p>
            <p class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">&#8377;{{ formattedPrice }}</p>
            <p class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-slate-500">
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm availability before visiting the seller
            </p>
        </div>

        <div class="space-y-3 pt-5">
            <button
                v-if="!revealedContact"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-orange-500 px-4 py-4 text-base font-black text-white shadow-md transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-lg"
                @click="$emit('request-contact')"
            >
                View Seller Contact
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </button>

            <div v-else class="space-y-3">
                <div class="rounded-lg border border-teal-200 bg-teal-50 p-4 text-center text-xl font-black text-teal-800">
                    {{ revealedContact }}
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a :href="'tel:' + revealedContact" class="flex items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-700">
                        Call
                    </a>
                    <a :href="'https://wa.me/91' + revealedContact + '?text=Hi, I am interested in your ' + title" target="_blank" class="flex items-center justify-center gap-2 rounded-lg bg-[#25D366] px-4 py-3 text-sm font-black text-white transition hover:bg-[#1DA851]">
                        WhatsApp
                    </a>
                </div>
            </div>

            <button class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                Send Enquiry
            </button>
        </div>

        <div class="mt-5 rounded-lg border border-teal-100 bg-teal-50 p-4">
            <p class="flex items-start gap-2 text-xs font-semibold leading-5 text-slate-500">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                OTP verification protects buyer and seller privacy before sharing contact details.
            </p>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    title: string;
    model: string;
    variant: string;
    price: number;
    city: string;
    state: string | null;
    revealedContact: string | null;
}>();

defineEmits(['request-contact']);

const formattedPrice = computed(() => props.price?.toLocaleString('en-IN') || '0');
</script>
