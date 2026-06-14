<template>
    <Head title="Dealer Dashboard" />
    <DealerLayout title="Dashboard" eyebrow="Showroom command center">
        <section class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Welcome back, {{ dealer.first_name }}</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">Keep your inventory moving and leads warm.</h2>
                <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Review stock, account limits and recent buyer interest from one place.</p>
            </div>
            <Link :href="actions.addCar" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-orange-500 px-5 text-sm font-black text-white transition hover:bg-orange-600">Add new car</Link>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Metric label="Total cars" :value="stats.total_cars" />
            <Metric label="Approved" :value="stats.approved_cars" tone="teal" />
            <Metric label="Pending review" :value="stats.pending_cars" tone="orange" />
            <Metric label="New enquiries" :value="stats.new_enquiries" tone="red" />
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-lg bg-slate-950 p-5 text-white shadow-sm sm:p-6">
                <p class="text-xs font-black uppercase tracking-wide text-teal-300">Available wallet balance</p>
                <p class="mt-3 text-4xl font-black">{{ money(stats.wallet_balance) }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <Link :href="actions.wallet" class="rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-black text-white hover:bg-teal-500">Manage wallet</Link>
                    <Link :href="actions.plans" class="rounded-lg border border-white/20 px-4 py-2.5 text-sm font-black text-white hover:bg-white/10">View plans</Link>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <Metric label="Remaining listings" :value="stats.remaining_listings" tone="blue" />
                <div class="rounded-lg border p-4 shadow-sm" :class="statusTone">
                    <p class="text-2xl font-black capitalize text-slate-950">{{ dealer.status }}</p>
                    <p class="mt-1 text-xs font-black uppercase tracking-wide text-slate-500">Showroom status</p>
                    <Link v-if="dealer.status !== 'approved'" :href="actions.profile" class="mt-4 inline-flex text-sm font-black text-teal-700">Complete profile</Link>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Share catalog</p>
                <h3 class="mt-2 text-xl font-black text-slate-950">Send buyers directly to your live inventory.</h3>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <input class="dealer-input min-w-0 flex-1" :value="dealer.catalog_url" readonly aria-label="Dealer catalog URL" />
                    <button type="button" class="min-h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white hover:bg-teal-700" @click="copyCatalog">{{ copied ? 'Copied' : 'Copy link' }}</button>
                </div>
            </div>
            <div class="rounded-lg border border-orange-100 bg-orange-50 p-5">
                <p class="text-xs font-black uppercase tracking-wide text-orange-700">Inventory pulse</p>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div><p class="text-3xl font-black text-slate-950">{{ stats.active_cars }}</p><p class="text-xs font-black uppercase text-slate-500">Live</p></div>
                    <div><p class="text-3xl font-black text-orange-700">{{ stats.active_pending_cars }}</p><p class="text-xs font-black uppercase text-slate-500">In review</p></div>
                </div>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div><p class="text-xs font-black uppercase tracking-wide text-teal-700">Buyer interest</p><h3 class="mt-1 text-xl font-black text-slate-950">Recent enquiries</h3></div>
                <Link :href="actions.enquiries" class="text-sm font-black text-teal-700">View all</Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[780px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Car</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Received</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in recentEnquiries" :key="item.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4"><Link :href="item.show_url" class="font-black text-slate-950 hover:text-teal-700">{{ item.customer_name }}</Link></td>
                            <td class="px-5 py-4"><Link v-if="item.car" :href="item.car.edit_url" class="font-bold text-slate-700 hover:text-teal-700">{{ item.car.title }}</Link><span v-else class="text-slate-400">Car unavailable</span></td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ item.customer_phone }}</td>
                            <td class="px-5 py-4"><Status :value="item.status" /></td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ item.created_at }}</td>
                        </tr>
                        <tr v-if="!recentEnquiries.length"><td colspan="5" class="px-5 py-12 text-center"><p class="font-black text-slate-950">No enquiries yet</p><p class="mt-1 text-sm font-semibold text-slate-500">Add inventory and share your catalog to attract buyers.</p></td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </DealerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DealerLayout from '@/Layouts/DealerLayout.vue';

const props = defineProps<{
    stats: { total_cars: number; approved_cars: number; pending_cars: number; active_cars: number; active_pending_cars: number; new_enquiries: number; wallet_balance: number; remaining_listings: number };
    dealer: { first_name: string; status: string; catalog_url: string };
    recentEnquiries: Array<{ id: number; customer_name: string; customer_phone: string; status: string; created_at: string; car: { title: string; edit_url: string } | null; show_url: string }>;
    actions: { addCar: string; inventory: string; wallet: string; plans: string; enquiries: string; profile: string };
}>();

const copied = ref(false);
const statusTone = computed(() => props.dealer.status === 'approved' ? 'border-teal-100 bg-teal-50' : props.dealer.status === 'pending' ? 'border-orange-100 bg-orange-50' : 'border-red-100 bg-red-50');
const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value || 0)}`;
const copyCatalog = async () => {
    await navigator.clipboard.writeText(props.dealer.catalog_url);
    copied.value = true;
    window.setTimeout(() => { copied.value = false; }, 1800);
};

const Metric = defineComponent({
    props: { label: { type: String, required: true }, value: { type: [String, Number], required: true }, tone: { type: String, default: 'slate' } },
    setup(metricProps) {
        const tones: Record<string, string> = { slate: 'border-slate-200 bg-white', teal: 'border-teal-100 bg-teal-50', orange: 'border-orange-100 bg-orange-50', red: 'border-red-100 bg-red-50', blue: 'border-blue-100 bg-blue-50' };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', tones[metricProps.tone]] }, [
            h('p', { class: 'text-2xl font-black text-slate-950' }, typeof metricProps.value === 'number' ? new Intl.NumberFormat('en-IN').format(metricProps.value) : metricProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide text-slate-500' }, metricProps.label),
        ]);
    },
});
const Status = defineComponent({
    props: { value: { type: String, required: true } },
    setup(statusProps) {
        return () => h('span', { class: ['inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize', statusProps.value === 'new' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'bg-slate-100 text-slate-600'] }, statusProps.value);
    },
});
</script>
