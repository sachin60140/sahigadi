<template>
    <Head title="Service History Reports" />
    <AdminLayout title="Service History Reports" eyebrow="Service operations">
        <ServiceHistoryTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Dealer service ledger</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Review workshop history lookups and service depth.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">Track dealer usage, API outcomes, record counts and service charges with direct access to each workshop timeline.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a :href="actions.exportExcel" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-800">Export Excel</a>
                    <a :href="actions.exportPdf" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export PDF</a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <Metric label="Total searches" :value="stats.total" />
            <Metric label="Successful" :value="stats.successful" tone="teal" />
            <Metric label="Failed" :value="stats.failed" tone="red" />
            <Metric label="Revenue" :value="money(stats.revenue)" tone="blue" />
            <Metric label="Dealer charge" :value="money(stats.charge)" tone="orange" />
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.1fr_1fr_160px_170px_170px_auto]" @submit.prevent="filter">
                <Field label="Vehicle number"><input v-model="form.search" class="admin-input uppercase" type="search" placeholder="BR01AB1234" /></Field>
                <Field label="Dealer"><select v-model="form.dealer_id" class="admin-input"><option value="">All dealers</option><option v-for="dealer in dealers" :key="dealer.id" :value="String(dealer.id)">{{ dealer.name }}</option></select></Field>
                <Field label="Status"><select v-model="form.status" class="admin-input"><option value="">All statuses</option><option value="success">Successful</option><option value="failed">Failed</option></select></Field>
                <Field label="From"><input v-model="form.from_date" class="admin-input" type="date" /></Field>
                <Field label="To"><input v-model="form.to_date" class="admin-input" type="date" /></Field>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-1">
                    <button class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white hover:bg-teal-700">Filter</button>
                    <Link href="/admin/service-histories" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Clear</Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1030px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Vehicle</th><th class="px-5 py-3">Dealer</th><th class="px-5 py-3">Records</th><th class="px-5 py-3">Outcome</th><th class="px-5 py-3">Charge</th><th class="px-5 py-3">Created</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in searches.data" :key="item.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4"><p class="font-semibold uppercase text-slate-950">{{ item.vehicle_number }}</p><p class="mt-1 text-xs font-semibold text-slate-400">#{{ item.id }}</p></td>
                            <td class="px-5 py-4"><Link v-if="item.dealer" :href="item.dealer.show_url" class="font-semibold text-slate-950 hover:text-teal-700">{{ item.dealer.name }}</Link><p v-else class="font-bold text-slate-400">Dealer unavailable</p><p v-if="item.dealer?.phone" class="mt-1 text-xs font-semibold text-slate-500">{{ item.dealer.phone }}</p></td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ item.service_count }} services</td>
                            <td class="px-5 py-4"><Status :success="item.is_success" /><p v-if="item.error_message" class="mt-2 max-w-[220px] truncate text-xs font-bold text-red-600">{{ item.error_message }}</p></td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ money(item.charge_amount) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-600">{{ item.created_at || 'N/A' }}</td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-2"><Link :href="item.actions.show" class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700">View</Link><a v-if="item.is_success" :href="item.actions.pdf" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">PDF</a></div></td>
                        </tr>
                        <tr v-if="!searches.data.length"><td colspan="7" class="px-5 py-14 text-center"><p class="text-lg font-semibold text-slate-950">No service searches found</p><p class="mt-2 text-sm font-semibold text-slate-500">Try changing or clearing the filters.</p></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="searches.links" /></div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';
import ServiceHistoryTabs from '@/Components/Admin/ServiceHistoryTabs.vue';

type Item = { id:number; vehicle_number:string; service_count:number; charge_amount:number; is_success:boolean; error_message?:string|null; created_at?:string|null; dealer?:{name:string;phone?:string|null;show_url:string}|null; actions:{show:string;pdf:string} };
const props = defineProps<{ searches:{data:Item[];links:Array<{url:string|null;label:string;active:boolean}>}; dealers:Array<{id:number;name:string}>; filters:{search:string;dealer_id:string;status:string;from_date:string;to_date:string}; stats:{total:number;successful:number;failed:number;revenue:number;charge:number}; actions:{exportExcel:string;exportPdf:string} }>();
const form = reactive({ ...props.filters });
const money = (value:number|string) => `Rs ${new Intl.NumberFormat('en-IN',{maximumFractionDigits:2}).format(Number(value||0))}`;
const filter = () => { const params:Record<string,string>={}; Object.entries(form).forEach(([k,v])=>{if(v)params[k]=v;}); router.get('/admin/service-histories',params,{preserveState:true,preserveScroll:true}); };
const Field=defineComponent({props:{label:{type:String,required:true}},setup(p,{slots}){return()=>h('label',[h('span',{class:'mb-2 block text-sm font-semibold text-slate-700'},p.label),slots.default?.()]);}});
const Metric=defineComponent({props:{label:{type:String,required:true},value:{type:[String,Number],required:true},tone:{type:String,default:'slate'}},setup(p){const c:any={slate:'border-slate-200 bg-white',teal:'border-teal-100 bg-teal-50',red:'border-red-100 bg-red-50',blue:'border-blue-100 bg-blue-50',orange:'border-orange-100 bg-orange-50'};return()=>h('div',{class:['rounded-lg border p-4 shadow-sm',c[p.tone]]},[h('p',{class:'text-2xl font-semibold text-slate-950'},typeof p.value==='number'?new Intl.NumberFormat('en-IN').format(p.value):p.value),h('p',{class:'mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500'},p.label)]);}});
const Status=defineComponent({props:{success:{type:Boolean,required:true}},setup(p){return()=>h('span',{class:['inline-flex rounded-md px-2.5 py-1 text-xs font-semibold',p.success?'bg-teal-50 text-teal-700 ring-1 ring-teal-100':'bg-red-50 text-red-700 ring-1 ring-red-100']},p.success?'Successful':'Failed');}});
</script>
