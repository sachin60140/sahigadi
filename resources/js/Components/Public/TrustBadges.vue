<template>
    <section class="bg-white">
        <div class="mx-auto max-w-[1440px] px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-3 rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
                <TrustItem
                    title="Verified Listings"
                    subtitle="Cars checked before approval"
                    icon-path="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
                <TrustItem
                    title="Secure Contact"
                    subtitle="OTP unlock protects buyers"
                    icon-path="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                />
                <TrustItem
                    title="Trusted Dealers"
                    subtitle="Local verified dealer network"
                    icon-path="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"
                />
                <TrustItem
                    title="Transparent Pricing"
                    subtitle="Clear listing prices in INR"
                    icon-path="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';

const TrustItem = defineComponent({
    props: {
        title: { type: String, required: true },
        subtitle: { type: String, required: true },
        iconPath: { type: String, required: true },
    },
    setup(props) {
        return () => h('div', { class: 'flex items-center gap-4 rounded-lg bg-slate-50 p-4' }, [
            h('span', { class: 'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-teal-700 text-white' }, [
                h('svg', { class: 'h-5 w-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: props.iconPath }),
                ]),
            ]),
            h('span', [
                h('span', { class: 'block text-sm font-black text-slate-950' }, props.title),
                h('span', { class: 'mt-1 block text-xs font-semibold text-slate-500' }, props.subtitle),
            ]),
        ]);
    },
});
</script>
