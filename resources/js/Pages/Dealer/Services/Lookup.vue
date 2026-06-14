<template>
    <Head :title="page.title" />
    <DealerLayout :title="page.title" :eyebrow="page.eyebrow">
        <section class="grid gap-3 sm:grid-cols-3">
            <Metric label="Wallet balance" :value="money(walletBalance)" tone="teal" />
            <Metric label="Cost per search" :value="money(charge)" tone="blue" />
            <Metric label="Total searches" :value="searches.total || searches.data.length" />
        </section>

        <section class="mt-5 grid gap-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm xl:grid-cols-[0.9fr_1.1fr] xl:items-center">
            <div><p class="text-xs font-black uppercase tracking-wide text-teal-700">New lookup</p><h2 class="mt-2 text-2xl font-black text-slate-950">{{ page.title }}</h2><p class="mt-2 text-sm font-semibold leading-7 text-slate-600">{{ page.description }}</p></div>
            <form class="rounded-lg bg-slate-50 p-4" @submit.prevent="submit">
                <label><span class="mb-2 block text-sm font-black text-slate-700">{{ page.inputLabel }}</span><input v-model="lookupForm.value" class="dealer-input uppercase" :placeholder="page.placeholder" maxlength="20" required /></label>
                <button class="mt-3 w-full rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white hover:bg-teal-700 disabled:opacity-50" :disabled="lookupForm.processing || walletBalance < charge">{{ lookupForm.processing?'Searching...':'Run search' }}</button>
                <p v-if="walletBalance < charge" class="mt-3 text-sm font-bold text-red-600">Insufficient balance. <Link :href="actions.wallet" class="underline">Recharge wallet</Link></p>
                <p v-if="lookupForm.errors[page.inputName]" class="mt-2 text-xs font-bold text-red-600">{{ lookupForm.errors[page.inputName] }}</p>
            </form>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_180px_auto]" @submit.prevent="filter">
                <label><span class="mb-2 block text-sm font-black text-slate-700">Vehicle search</span><input v-model="filters.search" class="dealer-input uppercase" type="search" placeholder="Vehicle number" /></label>
                <label><span class="mb-2 block text-sm font-black text-slate-700">Status</span><select v-model="filters.status" class="dealer-input"><option value="">All statuses</option><option value="success">Successful</option><option value="failed">Failed</option></select></label>
                <div class="flex items-end gap-2"><button class="h-12 rounded-lg bg-teal-700 px-5 text-sm font-black text-white">Filter</button><Link :href="page.indexUrl" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700">Clear</Link></div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto"><table class="w-full min-w-[980px] text-left text-sm"><thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Vehicle</th><th class="px-5 py-3">Result</th><th class="px-5 py-3">Records</th><th class="px-5 py-3">Charge</th><th class="px-5 py-3">Created</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
                <tr v-for="item in searches.data" :key="item.id" class="hover:bg-slate-50"><td class="px-5 py-4"><p class="font-black uppercase text-slate-950">{{ item.vehicle_number }}</p><p v-if="item.secondary" class="mt-1 max-w-xs truncate text-xs font-semibold text-slate-500">{{ item.secondary }}</p></td><td class="px-5 py-4"><Status :success="item.is_success" /><p v-if="item.error" class="mt-1 max-w-xs truncate text-xs font-bold text-red-600">{{ item.error }}</p></td><td class="px-5 py-4 font-black text-slate-950">{{ item.record_count ?? '-' }}<p v-if="item.total_amount!==null" class="mt-1 text-xs text-orange-700">{{ money(item.total_amount) }} total</p></td><td class="px-5 py-4 font-black text-slate-950">{{ money(item.charge) }}</td><td class="px-5 py-4 font-semibold text-slate-600">{{ item.created_at }}</td><td class="px-5 py-4"><div class="flex justify-end gap-2"><a v-if="item.actions.pdf" :href="item.actions.pdf" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700">PDF</a><Link :href="item.actions.show" class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-black text-white">View</Link></div></td></tr>
                <tr v-if="!searches.data.length"><td colspan="6" class="px-5 py-14 text-center"><p class="text-lg font-black text-slate-950">No searches yet</p><p class="mt-2 text-sm font-semibold text-slate-500">Run your first lookup above.</p></td></tr>
            </tbody></table></div><div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="searches.links" /></div>
        </section>
    </DealerLayout>
</template>
<script setup lang="ts">
import{defineComponent,h,reactive}from'vue';import{Head,Link,router,useForm}from'@inertiajs/vue3';import DealerLayout from '@/Layouts/DealerLayout.vue';import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';
const props=defineProps<{page:any;charge:number;walletBalance:number;searches:any;filters:{search:string;status:string};actions:{search:string;wallet:string}}>();const lookupForm=useForm({value:''} as Record<string,any>);const filters=reactive({...props.filters});const submit=()=>lookupForm.transform(()=>({[props.page.inputName]:lookupForm.value})).post(props.actions.search,{preserveScroll:true});const filter=()=>router.get(props.page.indexUrl,filters,{preserveState:true,preserveScroll:true});const money=(v:number)=>`Rs ${new Intl.NumberFormat('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}).format(Number(v||0))}`;
const Metric=defineComponent({props:{label:{type:String,required:true},value:{type:[String,Number],required:true},tone:{type:String,default:'slate'}},setup(p){const c:Record<string,string>={slate:'border-slate-200 bg-white',teal:'border-teal-100 bg-teal-50',blue:'border-blue-100 bg-blue-50'};return()=>h('div',{class:['rounded-lg border p-4 shadow-sm',c[p.tone]]},[h('p',{class:'text-2xl font-black text-slate-950'},p.value),h('p',{class:'mt-1 text-xs font-black uppercase tracking-wide text-slate-500'},p.label)]);}});const Status=defineComponent({props:{success:{type:Boolean,required:true}},setup(p){return()=>h('span',{class:['inline-flex rounded-md px-2.5 py-1 text-xs font-black',p.success?'bg-teal-50 text-teal-700':'bg-red-50 text-red-700']},p.success?'Successful':'Failed');}});
</script>
