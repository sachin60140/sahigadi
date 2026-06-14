<template>
    <Head :title="`Service History ${search.vehicle_number}`" />
    <AdminLayout title="Service History Details" eyebrow="Service operations">
        <ServiceHistoryTabs />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <Link :href="search.actions.back" class="inline-flex rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50">Back to reports</Link>
                    <p class="mt-5 text-xs font-black uppercase tracking-wide text-teal-700">Workshop timeline</p>
                    <h2 class="mt-2 text-3xl font-black uppercase text-slate-950">{{ search.vehicle_number }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ search.dealer?.name || 'Dealer unavailable' }} / {{ search.created_at || 'Date unavailable' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Status :success="search.is_success" />
                    <a v-if="search.is_success" :href="search.actions.pdf" class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white hover:bg-teal-700">Download PDF</a>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-3 sm:grid-cols-3">
            <Tile label="Services found" :value="`${search.service_count} records`" />
            <Tile label="Amount charged" :value="money(search.charge_amount)" tone="teal" />
            <Tile label="Data source" :value="search.has_extended_record ? 'Detailed workshop record' : 'Search summary only'" tone="orange" />
        </section>

        <section v-if="search.error_message" class="mt-5 rounded-lg border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ search.error_message }}</section>
        <section v-else-if="search.is_success && !search.has_extended_record" class="mt-5 rounded-lg border border-orange-100 bg-orange-50 px-5 py-4 text-sm font-bold text-orange-800">The lookup succeeded, but its extended workshop record is no longer available.</section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4"><p class="text-xs font-black uppercase tracking-wide text-teal-700">Service records</p><h3 class="mt-1 text-xl font-black text-slate-950">{{ search.records.length }} workshop visits</h3></div>
            <div v-if="search.records.length" class="overflow-x-auto">
                <table class="w-full min-w-[1080px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Workshop</th><th class="px-5 py-3">Service</th><th class="px-5 py-3">RO / Bill</th><th class="px-5 py-3">Mileage</th><th class="px-5 py-3">Bill amount</th><th class="px-5 py-3">Paid</th></tr></thead>
                    <tbody class="divide-y divide-slate-100"><tr v-for="(record,index) in search.records" :key="index"><td class="px-5 py-4 font-black text-slate-950">{{ record.date || 'N/A' }}</td><td class="px-5 py-4"><p class="font-black text-slate-950">{{ record.dealer_name || 'N/A' }}</p><p class="mt-1 text-xs font-semibold text-slate-500">{{ record.location_name || '' }}</p></td><td class="px-5 py-4"><p class="font-bold text-slate-700">{{ record.work_type || 'N/A' }}</p><p class="mt-1 text-xs font-semibold text-slate-500">{{ record.service_category || '' }}</p></td><td class="px-5 py-4 font-semibold text-slate-600">{{ record.repair_order_no || 'N/A' }} / {{ record.bill_no || 'N/A' }}</td><td class="px-5 py-4 font-semibold text-slate-600">{{ record.mileage || 'N/A' }}</td><td class="px-5 py-4 font-black text-slate-950">{{ money(record.bill_amount) }}</td><td class="px-5 py-4 font-black text-teal-700">{{ money(record.paid_amount) }}</td></tr></tbody>
                </table>
            </div>
            <div v-else class="px-5 py-14 text-center"><p class="text-lg font-black text-slate-950">No service rows available</p><p class="mt-2 text-sm font-semibold text-slate-500">The search summary remains available above.</p></div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent,h } from 'vue';
import { Head,Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ServiceHistoryTabs from '@/Components/Admin/ServiceHistoryTabs.vue';
type RecordRow={date?:string|null;dealer_name?:string|null;location_name?:string|null;work_type?:string|null;service_category?:string|null;bill_amount:number;paid_amount:number;mileage?:string|number|null;repair_order_no?:string|null;bill_no?:string|null};
type Search={vehicle_number:string;service_count:number;charge_amount:number;is_success:boolean;error_message?:string|null;created_at?:string|null;dealer?:{name:string}|null;records:RecordRow[];has_extended_record:boolean;actions:{back:string;pdf:string}};
defineProps<{search:Search}>();
const money=(v:number|string)=>`Rs ${new Intl.NumberFormat('en-IN',{maximumFractionDigits:2}).format(Number(v||0))}`;
const Status=defineComponent({props:{success:{type:Boolean,required:true}},setup(p){return()=>h('span',{class:['inline-flex rounded-md px-3 py-2 text-xs font-black',p.success?'bg-teal-50 text-teal-700 ring-1 ring-teal-100':'bg-red-50 text-red-700 ring-1 ring-red-100']},p.success?'Successful lookup':'Failed lookup');}});
const Tile=defineComponent({props:{label:{type:String,required:true},value:{type:String,required:true},tone:{type:String,default:'slate'}},setup(p){const c:any={slate:'border-slate-200 bg-white',teal:'border-teal-100 bg-teal-50',orange:'border-orange-100 bg-orange-50'};return()=>h('div',{class:['rounded-lg border p-4 shadow-sm',c[p.tone]]},[h('p',{class:'text-xs font-black uppercase tracking-wide text-slate-500'},p.label),h('p',{class:'mt-2 text-lg font-black text-slate-950'},p.value)]);}});
</script>
