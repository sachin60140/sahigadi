<template>
    <Head title="Promote Listing" />
    <CustomerLayout title="Promote Listing" eyebrow="Featured inventory">
        <section class="grid gap-5 xl:grid-cols-[1fr_280px]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <Link :href="actions.dashboard" class="inline-flex items-center gap-2 text-sm font-black text-teal-700"><ArrowLeft class="h-4 w-4" /> Back to dashboard</Link>
                <h2 class="mt-4 text-3xl font-black text-slate-950">{{ listing.title }}</h2>
                <p class="mt-2 text-sm font-semibold text-slate-600">Feature this listing across high-visibility marketplace placements.</p>
                <p v-if="listing.is_featured" class="mt-4 rounded-lg border border-amber-100 bg-amber-50 p-3 text-sm font-bold text-amber-800">Currently featured until {{ listing.featured_expires_at }}. New days are added to the existing expiry.</p>
            </div>
            <div class="rounded-lg bg-slate-950 p-5 text-white">
                <p class="text-xs font-black uppercase text-teal-300">Wallet balance</p>
                <p class="mt-2 text-3xl font-black">{{ money(walletBalance) }}</p>
                <Link :href="actions.wallet" class="mt-4 inline-flex text-sm font-black text-orange-300">Recharge wallet</Link>
            </div>
        </section>

        <section class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article v-for="(plan, index) in plans" :key="plan.id" class="rounded-lg border bg-white p-5 shadow-sm" :class="index === 0 ? 'border-orange-200' : 'border-slate-200'">
                <span v-if="index === 0" class="inline-flex rounded-md bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-700">Popular</span>
                <h3 class="mt-3 text-xl font-black text-slate-950">{{ plan.name }}</h3>
                <p class="mt-2 text-3xl font-black text-teal-700">{{ money(plan.price) }}</p>
                <p class="mt-1 text-sm font-bold text-slate-500">{{ plan.duration_days }} days</p>
                <ul class="mt-5 space-y-2 text-sm font-semibold text-slate-600"><li>Homepage visibility</li><li>Priority search placement</li><li>Featured listing badge</li><li>Rotating promotion slots</li></ul>
                <button type="button" class="mt-6 w-full rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white disabled:opacity-50" :disabled="processing === plan.id" @click="purchase(plan)">{{ processing === plan.id ? 'Processing...' : 'Buy with wallet' }}</button>
            </article>
            <div v-if="!plans.length" class="rounded-lg border border-slate-200 bg-white p-8 text-center md:col-span-2 xl:col-span-3"><p class="font-black text-slate-950">No featured plans available</p></div>
        </section>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
type Plan = { id: number; name: string; price: number; duration_days: number };
const props = defineProps<{ listing: any; plans: Plan[]; walletBalance: number; actions: { dashboard: string; purchase: string; wallet: string } }>();
const processing = ref<number | null>(null);
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN').format(Number(value || 0))}`;
const purchase = (plan: Plan) => {
    if (plan.price > props.walletBalance) {
        window.location.href = props.actions.wallet;
        return;
    }
    if (!window.confirm(`Use ${money(plan.price)} from your wallet?`)) return;
    processing.value = plan.id;
    router.post(props.actions.purchase, { plan_id: plan.id }, { onFinish: () => { processing.value = null; } });
};
</script>
