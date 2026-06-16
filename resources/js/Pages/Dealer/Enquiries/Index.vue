<template>
    <Head title="Dealer Enquiries" />
    <DealerLayout title="Enquiries" eyebrow="Buyer leads">
        <section class="grid gap-3 sm:grid-cols-3">
            <Metric label="All enquiries" :value="stats.total" />
            <Metric label="New leads" :value="stats.new" tone="red" />
            <Metric label="Contacted" :value="stats.contacted" tone="teal" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="flex flex-col gap-3 sm:flex-row sm:items-end" @submit.prevent="filter">
                <label class="w-full sm:max-w-xs">
                    <span class="mb-2 block text-sm font-semibold text-slate-700">Lead status</span>
                    <select v-model="status" class="dealer-input"><option value="all">All statuses</option><option value="new">New</option><option value="contacted">Contacted</option></select>
                </label>
                <div class="flex gap-2">
                    <button class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white hover:bg-teal-700">Filter</button>
                    <Link href="/dealer/enquiries" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Clear</Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Car</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Received</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in enquiries.data" :key="item.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ item.customer_name }}</td>
                            <td class="px-5 py-4"><a :href="item.call_url" class="font-semibold text-teal-700">{{ item.customer_phone }}</a></td>
                            <td class="px-5 py-4"><a v-if="item.car_url" :href="item.car_url" target="_blank" rel="noopener noreferrer" class="font-bold text-slate-700 hover:text-teal-700">{{ item.car_title }}</a><span v-else class="text-slate-400">Car unavailable</span></td>
                            <td class="px-5 py-4"><Status :value="item.status" /></td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ item.created_at }}</td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-2"><a :href="item.whatsapp_url" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-teal-200 px-3 py-2 text-xs font-semibold text-teal-700 hover:bg-teal-50">WhatsApp</a><Link :href="item.show_url" class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700">View</Link></div></td>
                        </tr>
                        <tr v-if="!enquiries.data.length"><td colspan="6" class="px-5 py-14 text-center"><p class="text-lg font-semibold text-slate-950">No enquiries found</p><p class="mt-2 text-sm font-semibold text-slate-500">Try another status filter.</p></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="enquiries.links" /></div>
        </section>
    </DealerLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DealerLayout from '@/Layouts/DealerLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Item = { id: number; customer_name: string; customer_phone: string; status: string; created_at: string; car_title: string | null; car_url: string | null; show_url: string; call_url: string; whatsapp_url: string };
const props = defineProps<{ enquiries: { data: Item[]; links: Array<{ url: string | null; label: string; active: boolean }> }; filters: { status: string }; stats: { total: number; new: number; contacted: number } }>();
const status = ref(props.filters.status || 'all');
const filter = () => router.get('/dealer/enquiries', status.value === 'all' ? {} : { status: status.value }, { preserveState: true, preserveScroll: true });
const Metric = defineComponent({ props: { label: { type: String, required: true }, value: { type: Number, required: true }, tone: { type: String, default: 'slate' } }, setup(p) { const c: Record<string, string> = { slate: 'border-slate-200 bg-white', red: 'border-red-100 bg-red-50', teal: 'border-teal-100 bg-teal-50' }; return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', c[p.tone]] }, [h('p', { class: 'text-2xl font-semibold text-slate-950' }, new Intl.NumberFormat('en-IN').format(p.value)), h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500' }, p.label)]); } });
const Status = defineComponent({ props: { value: { type: String, required: true } }, setup(p) { return () => h('span', { class: ['inline-flex rounded-md px-2.5 py-1 text-xs font-semibold capitalize', p.value === 'new' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'bg-slate-100 text-slate-600'] }, p.value); } });
</script>
