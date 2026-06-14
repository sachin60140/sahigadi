<template>
    <Head>
        <title>{{ paid ? 'Payment Complete' : 'Payment Link Expired' }}</title>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <PublicLayout>
        <section class="bg-[#f7f9fb] px-4 py-12 sm:py-20">
            <div class="mx-auto max-w-lg rounded-lg border border-slate-200 bg-white p-7 text-center shadow-sm sm:p-9">
                <span :class="paid ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-600'" class="mx-auto grid h-16 w-16 place-items-center rounded-lg">
                    <CircleCheck v-if="paid" class="h-9 w-9" />
                    <ClockAlert v-else class="h-9 w-9" />
                </span>
                <p class="mt-6 text-xs font-black uppercase tracking-wide" :class="paid ? 'text-teal-700' : 'text-red-600'">
                    {{ paid ? 'Payment verified' : 'Link unavailable' }}
                </p>
                <h1 class="mt-2 text-3xl font-black text-slate-950">{{ paid ? 'Payment already completed' : 'Payment link expired' }}</h1>
                <p class="mt-4 text-sm font-semibold leading-7 text-slate-600">
                    {{ paid ? 'This payment link has been successfully processed.' : 'This link is no longer valid. Please contact the issuer to request a new payment link.' }}
                </p>
                <p v-if="purpose" class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">{{ purpose }}</p>
                <Link :href="homeUrl" class="mt-7 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-black text-white hover:bg-slate-800">
                    <Home class="h-4 w-4" />
                    Return home
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { CircleCheck, ClockAlert, Home } from '@lucide/vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps<{ status: string; purpose?: string | null; homeUrl: string }>();
const paid = computed(() => props.status === 'paid');
</script>
