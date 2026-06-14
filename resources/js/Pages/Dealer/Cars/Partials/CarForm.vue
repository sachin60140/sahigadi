<template>
    <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="submit">
        <div class="grid gap-4 md:grid-cols-2">
            <Field label="Listing title" :error="form.errors.title"><input v-model="form.title" class="dealer-input" required type="text" placeholder="Mahindra Bolero B6" /></Field>
            <Field label="Brand" :error="form.errors.brand_id"><select v-model="form.brand_id" class="dealer-input"><option value="">Select brand</option><option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option></select></Field>
            <Field label="Model" :error="form.errors.model"><input v-model="form.model" class="dealer-input" type="text" /></Field>
            <Field label="Year" :error="form.errors.year"><input v-model="form.year" class="dealer-input" type="number" min="1900" :max="currentYear" /></Field>
            <Field label="Fuel type" :error="form.errors.fuel_type"><select v-model="form.fuel_type" class="dealer-input"><option value="">Select fuel</option><option v-for="item in fuelTypes" :key="item.value" :value="item.value">{{ item.label }}</option></select></Field>
            <Field label="Transmission" :error="form.errors.transmission"><select v-model="form.transmission" class="dealer-input"><option value="">Select transmission</option><option v-for="item in transmissions" :key="item.value" :value="item.value">{{ item.label }}</option></select></Field>
            <Field label="Kilometers driven" :error="form.errors.km_driven"><input v-model="form.km_driven" class="dealer-input" type="number" min="0" /></Field>
            <Field label="Price" :error="form.errors.price"><input v-model="form.price" class="dealer-input" required type="number" min="0" step="1" /></Field>
            <Field label="City" :error="form.errors.city"><input v-model="form.city" class="dealer-input" type="text" /></Field>
            <Field label="Registration number" :error="form.errors.registration_number"><input v-model="form.registration_number" class="dealer-input uppercase" type="text" maxlength="20" /></Field>
            <Field label="Number of owners" :error="form.errors.owners"><select v-model="form.owners" class="dealer-input"><option v-for="owner in 5" :key="owner" :value="owner">{{ owner }}</option></select></Field>
            <div></div>
            <Field class="md:col-span-2" label="Description" :error="form.errors.description"><textarea v-model="form.description" class="dealer-input min-h-32" placeholder="Condition, service records and notable features"></textarea></Field>
            <Field class="md:col-span-2" :label="isEdit ? 'Add more images' : 'Vehicle images'" :error="form.errors.images">
                <input class="dealer-input file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-black" type="file" multiple accept="image/*" @change="selectImages" />
                <p class="mt-2 text-xs font-bold text-slate-500">Up to 10 JPG, PNG or GIF images, maximum 2 MB each.</p>
            </Field>
        </div>
        <div v-if="previews.length" class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            <button v-for="(preview,index) in previews" :key="preview" type="button" class="overflow-hidden rounded-lg border p-2 text-left" :class="form.primary_image_index===index?'border-teal-600 bg-teal-50':'border-slate-200'" @click="form.primary_image_index=index">
                <img :src="preview" alt="Selected vehicle" class="aspect-[4/3] w-full rounded-md object-cover" /><span class="mt-2 block text-xs font-black" :class="form.primary_image_index===index?'text-teal-700':'text-slate-500'">{{ form.primary_image_index===index?'Primary image':'Set primary' }}</span>
            </button>
        </div>
        <div class="mt-6 flex flex-wrap gap-3"><button class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white hover:bg-teal-800 disabled:opacity-60" :disabled="form.processing">{{ form.processing?'Saving...':submitLabel }}</button><Link :href="cancelUrl" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-black text-slate-700">Cancel</Link></div>
    </form>
</template>
<script setup lang="ts">
import {computed,defineComponent,h,onMounted} from 'vue'; import {Link,useForm} from '@inertiajs/vue3';
type Initial={title?:string;brand_id?:number|string|null;model?:string|null;year?:number|null;fuel_type?:string|null;transmission?:string|null;km_driven?:number|null;price?:number|null;description?:string|null;city?:string|null;latitude?:number|null;longitude?:number|null;registration_number?:string|null;owners?:number|null};
const props=defineProps<{initial?:Initial;brands:Array<{id:number;name:string}>;fuelTypes:Array<{value:string;label:string}>;transmissions:Array<{value:string;label:string}>;action:string;cancelUrl:string;isEdit?:boolean}>();
const form=useForm({title:props.initial?.title||'',brand_id:props.initial?.brand_id||'',model:props.initial?.model||'',year:props.initial?.year||'',fuel_type:props.initial?.fuel_type||'',transmission:props.initial?.transmission||'',km_driven:props.initial?.km_driven||'',price:props.initial?.price||'',description:props.initial?.description||'',city:props.initial?.city||'',latitude:props.initial?.latitude||'',longitude:props.initial?.longitude||'',registration_number:props.initial?.registration_number||'',owners:props.initial?.owners||1,images:[] as File[],primary_image_index:0,_method:props.isEdit?'put':'post'});
const currentYear=new Date().getFullYear(); const submitLabel=computed(()=>props.isEdit?'Update car details':'Submit for review'); const previews=computed(()=>form.images.map(file=>URL.createObjectURL(file))); const selectImages=(event:Event)=>{form.images=Array.from((event.target as HTMLInputElement).files||[]).slice(0,10);form.primary_image_index=0;}; const submit=()=>form.post(props.action,{forceFormData:true,preserveScroll:true});
onMounted(()=>{if(!form.latitude&&navigator.geolocation)navigator.geolocation.getCurrentPosition(position=>{form.latitude=position.coords.latitude;form.longitude=position.coords.longitude;},()=>{});});
const Field=defineComponent({props:{label:{type:String,required:true},error:{type:String,default:''}},setup(p,{slots}){return()=>h('label',[h('span',{class:'mb-2 block text-sm font-black text-slate-700'},p.label),slots.default?.(),p.error?h('p',{class:'mt-2 text-xs font-bold text-red-600'},p.error):null]);}});
</script>
