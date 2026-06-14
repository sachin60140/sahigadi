<template>
    <Head title="My Inventory" />
    <DealerLayout title="My Inventory" eyebrow="Showroom stock">
        <section class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-xs font-black uppercase tracking-wide text-teal-700">Inventory control</p><h2 class="mt-2 text-2xl font-black text-slate-950">Manage listings, reviews and promotion.</h2></div>
            <Link :href="actions.create" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-orange-500 px-5 text-sm font-black text-white hover:bg-orange-600">Add new car</Link>
        </section>
        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Metric label="All cars" :value="stats.total" /><Metric label="Approved" :value="stats.approved" tone="teal" /><Metric label="Pending" :value="stats.pending" tone="orange" /><Metric label="Rejected" :value="stats.rejected" tone="red" />
        </section>
        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 sm:grid-cols-[180px_minmax(0,1fr)_auto]" @submit.prevent="filter">
                <label><span class="mb-2 block text-sm font-black text-slate-700">Status</span><select v-model="form.status" class="dealer-input"><option value="all">All statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></label>
                <label><span class="mb-2 block text-sm font-black text-slate-700">Search</span><input v-model="form.search" class="dealer-input" type="search" placeholder="Search listing title" /></label>
                <div class="flex items-end gap-2"><button class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white hover:bg-teal-700">Filter</button><Link href="/dealer/cars" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700">Clear</Link></div>
            </form>
        </section>
        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto"><table class="w-full min-w-[1060px] text-left text-sm"><thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Listing</th><th class="px-5 py-3">Price</th><th class="px-5 py-3">Review status</th><th class="px-5 py-3">Promotion</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
                <tr v-for="car in cars.data" :key="car.id" class="hover:bg-slate-50">
                    <td class="px-5 py-4"><div class="flex items-center gap-3"><img v-if="car.image_url" :src="car.image_url" :alt="car.title" class="h-16 w-24 rounded-lg object-cover" /><div v-else class="grid h-16 w-24 place-items-center rounded-lg bg-slate-100 text-xs font-black text-slate-400">NO IMAGE</div><div><Link :href="car.actions.edit" class="font-black text-slate-950 hover:text-teal-700">{{ car.title }}</Link><p class="mt-1 text-xs font-bold text-slate-400">{{ car.unique_id }}</p><p v-if="car.rejection_reason" class="mt-1 max-w-xs text-xs font-bold text-red-600">{{ car.rejection_reason }}</p></div></div></td>
                    <td class="px-5 py-4 font-black text-slate-950">{{ money(car.price) }}</td>
                    <td class="px-5 py-4"><Status :value="car.status" /></td>
                    <td class="px-5 py-4"><span v-if="car.is_featured" class="inline-flex rounded-md bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-700 ring-1 ring-orange-100">Featured</span><span v-else class="font-semibold text-slate-400">Standard</span><p v-if="car.featured_expires_at" class="mt-1 text-xs font-bold text-slate-500">Until {{ car.featured_expires_at }}</p></td>
                    <td class="px-5 py-4"><div class="flex justify-end gap-2"><Link v-if="car.status === 'approved'" :href="car.actions.feature" class="rounded-lg border border-orange-200 px-3 py-2 text-xs font-black text-orange-700 hover:bg-orange-50">{{ car.is_featured ? 'Extend' : 'Feature' }}</Link><Link :href="car.actions.edit" class="rounded-lg bg-slate-950 px-3 py-2 text-xs font-black text-white hover:bg-teal-700">Edit</Link><button type="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-50" @click="remove(car)">Delete</button></div></td>
                </tr>
                <tr v-if="!cars.data.length"><td colspan="5" class="px-5 py-14 text-center"><p class="text-lg font-black text-slate-950">No cars found</p><Link :href="actions.create" class="mt-2 inline-flex text-sm font-black text-teal-700">Add your first car</Link></td></tr>
            </tbody></table></div><div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="cars.links" /></div>
        </section>
    </DealerLayout>
</template>
<script setup lang="ts">
import {defineComponent,h,reactive} from 'vue'; import {Head,Link,router} from '@inertiajs/vue3'; import DealerLayout from '@/Layouts/DealerLayout.vue'; import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';
type Car={id:number;unique_id:string;title:string;image_url:string|null;price:number;status:string;rejection_reason:string|null;is_featured:boolean;featured_expires_at:string|null;actions:{edit:string;delete:string;feature:string}};
const props=defineProps<{cars:{data:Car[];links:Array<{url:string|null;label:string;active:boolean}>};filters:{status:string;search:string};stats:{total:number;approved:number;pending:number;rejected:number};actions:{create:string}}>(); const form=reactive({...props.filters}); const money=(v:number)=>`Rs ${new Intl.NumberFormat('en-IN').format(v||0)}`; const filter=()=>router.get('/dealer/cars',{status:form.status,search:form.search},{preserveState:true,preserveScroll:true}); const remove=(car:Car)=>{if(window.confirm(`Delete ${car.title}?`))router.delete(car.actions.delete,{preserveScroll:true});};
const Metric=defineComponent({props:{label:{type:String,required:true},value:{type:Number,required:true},tone:{type:String,default:'slate'}},setup(p){const c:Record<string,string>={slate:'border-slate-200 bg-white',teal:'border-teal-100 bg-teal-50',orange:'border-orange-100 bg-orange-50',red:'border-red-100 bg-red-50'};return()=>h('div',{class:['rounded-lg border p-4 shadow-sm',c[p.tone]]},[h('p',{class:'text-2xl font-black text-slate-950'},new Intl.NumberFormat('en-IN').format(p.value)),h('p',{class:'mt-1 text-xs font-black uppercase tracking-wide text-slate-500'},p.label)]);}}); const Status=defineComponent({props:{value:{type:String,required:true}},setup(p){const c:Record<string,string>={approved:'bg-teal-50 text-teal-700 ring-1 ring-teal-100',pending:'bg-orange-50 text-orange-700 ring-1 ring-orange-100',rejected:'bg-red-50 text-red-700 ring-1 ring-red-100'};return()=>h('span',{class:['inline-flex rounded-md px-2.5 py-1 text-xs font-black capitalize',c[p.value]||'bg-slate-100 text-slate-600']},p.value);}});
</script>
