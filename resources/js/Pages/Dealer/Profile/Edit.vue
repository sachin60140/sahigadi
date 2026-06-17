<template>
    <Head title="My Profile" />
    <DealerLayout title="My Profile" eyebrow="Account and verification">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="h-1.5 bg-teal-700"></div>
            <div class="h-24 bg-slate-950 sm:h-28"></div>
            <div class="px-5 pb-5 sm:px-6">
                <div class="-mt-12 sm:-mt-14">
                    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-full border-4 border-white bg-slate-100 shadow-lg sm:h-28 sm:w-28">
                        <img :src="profilePreview" alt="Dealer profile" class="h-full w-full object-cover" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="truncate text-2xl font-bold tracking-tight text-slate-950">{{ dealer.name }}</h2>
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ dealer.company_name || 'Independent dealer' }} &middot; {{ dealer.dealer_unique_id }}</p>
                        <p class="mt-1 text-xs font-medium text-slate-400">{{ dealer.phone }}<span v-if="dealer.email"> &middot; {{ dealer.email }}</span></p>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1" :class="statusClass">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ accountStatus }}
                    </span>
                </div>
            </div>
        </section>

        <div class="mt-5 inline-flex gap-1 rounded-lg border border-slate-200 bg-white p-1 shadow-sm" role="tablist" aria-label="Profile sections">
            <button v-for="item in tabs" :key="item.value" type="button" class="rounded-md px-4 py-2 text-sm font-semibold transition" :class="tab===item.value?'bg-teal-700 text-white':'text-slate-600 hover:bg-slate-50'" @click="tab=item.value">{{ item.label }}</button>
        </div>

        <section v-if="tab==='profile'" class="mt-5 grid gap-5 xl:grid-cols-[300px_minmax(0,1fr)]">
            <aside class="flex flex-col gap-5 self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-950">Profile completion</p>
                        <span class="rounded-md bg-teal-50 px-2.5 py-1 text-sm font-bold text-teal-700">{{ dealer.completion }}%</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full transition-all" :class="dealer.completion>=100?'bg-teal-600':'bg-amber-500'" :style="{width:`${dealer.completion}%`}"></div></div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Documents</p>
                    <div class="mt-3 grid gap-2">
                        <DocStatus label="PAN document" :ok="hasPan" />
                        <DocStatus label="KYC document" :ok="hasKyc" />
                    </div>
                </div>

                <div v-if="dealer.missing_fields.length" class="border-t border-slate-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-orange-700">Still needed</p>
                    <div class="mt-3 flex flex-wrap gap-2"><span v-for="field in dealer.missing_fields" :key="field" class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700">{{ field }}</span></div>
                </div>
            </aside>

            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="saveProfile">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-teal-50 text-teal-700"><Building2 class="h-5 w-5" /></span>
                    <div><h3 class="text-base font-bold text-slate-950">Business identity</h3><p class="text-xs font-medium text-slate-500">Name, company and verification documents</p></div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <Field label="Full name" :error="profileForm.errors.name"><input v-model="profileForm.name" class="dealer-input" required /></Field>
                    <Field label="Company name" :error="profileForm.errors.company_name"><input v-model="profileForm.company_name" class="dealer-input" /></Field>
                    <Field label="Email address"><input :value="dealer.email" class="dealer-input bg-slate-50 text-slate-500" disabled /><p class="mt-2 text-xs font-medium text-slate-500">Email cannot be changed here.</p></Field>
                    <Field label="Profile photo" :error="profileForm.errors.profile_image"><input class="dealer-input file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold" type="file" accept="image/*" @change="selectProfileImage" /></Field>
                    <Field label="PAN number" :error="profileForm.errors.pan_number"><input v-model="profileForm.pan_number" class="dealer-input uppercase" maxlength="20" /></Field>
                    <Field label="PAN document" :error="profileForm.errors.pan_document_path"><input class="dealer-input" type="file" accept=".jpg,.jpeg,.png,.pdf" @change="profileForm.pan_document_path=fileFrom($event)" /><a v-if="dealer.pan_document_url" :href="dealer.pan_document_url" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-teal-700 hover:text-teal-800"><Eye class="h-3.5 w-3.5" /> View current document</a></Field>
                    <Field label="KYC document type" :error="profileForm.errors.kyc_document_type"><select v-model="profileForm.kyc_document_type" class="dealer-input"><option value="">Select type</option><option>Aadhaar</option><option>Voter ID</option><option>Passport</option><option>Driving License</option></select></Field>
                    <Field label="KYC document number" :error="profileForm.errors.kyc_document_number"><input v-model="profileForm.kyc_document_number" class="dealer-input" /></Field>
                    <Field label="KYC document" :error="profileForm.errors.kyc_document_path"><input class="dealer-input" type="file" accept=".jpg,.jpeg,.png,.pdf" @change="profileForm.kyc_document_path=fileFrom($event)" /><a v-if="dealer.kyc_document_url" :href="dealer.kyc_document_url" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-teal-700 hover:text-teal-800"><Eye class="h-3.5 w-3.5" /> View current document</a></Field>
                </div>

                <div class="mt-8 flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-teal-50 text-teal-700"><MapPin class="h-5 w-5" /></span>
                    <div><h3 class="text-base font-bold text-slate-950">Location</h3><p class="text-xs font-medium text-slate-500">Showroom address for buyers and invoices</p></div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <Field class="md:col-span-3" label="Full address" :error="profileForm.errors.address"><input v-model="profileForm.address" class="dealer-input" /></Field>
                    <Field label="City" :error="profileForm.errors.city"><input v-model="profileForm.city" class="dealer-input" /></Field>
                    <Field label="State" :error="profileForm.errors.state"><input v-model="profileForm.state" class="dealer-input" /></Field>
                    <Field label="Pincode" :error="profileForm.errors.pincode"><input v-model="profileForm.pincode" class="dealer-input" /></Field>
                </div>

                <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                    <button class="inline-flex items-center gap-2 rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:opacity-60" :disabled="profileForm.processing">{{ profileForm.processing?'Saving...':'Save profile' }}</button>
                    <span v-if="profileSaved" class="text-sm font-semibold text-teal-700">Saved</span>
                </div>
            </form>
        </section>

        <section v-else class="mt-5 grid gap-5 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-teal-50 text-teal-700"><Smartphone class="h-5 w-5" /></span>
                    <div><h3 class="text-base font-bold text-slate-950">Registered phone</h3><p class="text-xs font-medium text-slate-500">Current number: {{ dealer.phone }}</p></div>
                </div>
                <div class="mt-5 flex flex-col gap-2 sm:flex-row"><input v-model="newPhone" class="dealer-input" maxlength="10" inputmode="numeric" placeholder="New 10-digit number" :readonly="otpSent" /><button v-if="!otpSent" type="button" class="min-h-12 shrink-0 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:opacity-50" :disabled="sendingOtp" @click="sendOtp">{{ sendingOtp?'Sending...':'Request OTP' }}</button></div>
                <p v-if="otpMessage" class="mt-3 text-sm font-semibold" :class="otpError?'text-red-600':'text-teal-700'">{{ otpMessage }}</p>
                <form v-if="otpSent" class="mt-5 grid gap-4 sm:grid-cols-2" @submit.prevent="verifyPhone">
                    <Field label="OTP on current phone" :error="phoneForm.errors.old_otp"><input v-model="phoneForm.old_otp" class="dealer-input text-center tracking-[0.3em]" maxlength="6" inputmode="numeric" required /></Field>
                    <Field label="OTP on new phone" :error="phoneForm.errors.new_otp"><input v-model="phoneForm.new_otp" class="dealer-input text-center tracking-[0.3em]" maxlength="6" inputmode="numeric" required /></Field>
                    <button class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 sm:col-span-2" :disabled="phoneForm.processing">Verify and update phone</button>
                </form>
            </div>

            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="updatePassword">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-teal-50 text-teal-700"><Lock class="h-5 w-5" /></span>
                    <div><h3 class="text-base font-bold text-slate-950">Change password</h3><p class="text-xs font-medium text-slate-500">Use at least 8 characters</p></div>
                </div>
                <div class="mt-5 grid gap-4">
                    <Field label="Current password" :error="passwordForm.errors.current_password"><input v-model="passwordForm.current_password" class="dealer-input" type="password" required /></Field>
                    <Field label="New password" :error="passwordForm.errors.password"><input v-model="passwordForm.password" class="dealer-input" type="password" minlength="8" required /></Field>
                    <Field label="Confirm new password"><input v-model="passwordForm.password_confirmation" class="dealer-input" type="password" minlength="8" required /></Field>
                </div>
                <button class="mt-5 rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:opacity-60" :disabled="passwordForm.processing">Update password</button>
            </form>
        </section>
    </DealerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Building2, Eye, Lock, MapPin, Smartphone } from '@lucide/vue';
import DealerLayout from '@/Layouts/DealerLayout.vue';

const props=defineProps<{dealer:any;actions:{update:string;sendOtp:string;verifyPhone:string;updatePassword:string}}>();
const page=usePage();
const tab=ref('profile'); const tabs=[{value:'profile',label:'Profile and KYC'},{value:'security',label:'Security and phone'}];
const profilePreview=ref(props.dealer.profile_image_url); const newPhone=ref(''); const otpSent=ref(false); const sendingOtp=ref(false); const otpMessage=ref(''); const otpError=ref(false); const profileSaved=ref(false);

const accountStatus=computed(()=>(page.props as any).auth?.dealer?.status||'pending');
const statusClass=computed(()=>({approved:'bg-teal-50 text-teal-700 ring-teal-100',pending:'bg-orange-50 text-orange-700 ring-orange-100',rejected:'bg-red-50 text-red-700 ring-red-100'}[accountStatus.value as string]||'bg-slate-100 text-slate-600 ring-slate-200'));
const hasPan=computed(()=>Boolean(props.dealer.pan_document_url));
const hasKyc=computed(()=>Boolean(props.dealer.kyc_document_url));

const profileForm=useForm({name:props.dealer.name,company_name:props.dealer.company_name||'',address:props.dealer.address||'',city:props.dealer.city||'',state:props.dealer.state||'',pincode:props.dealer.pincode||'',pan_number:props.dealer.pan_number||'',kyc_document_type:props.dealer.kyc_document_type||'',kyc_document_number:props.dealer.kyc_document_number||'',profile_image:null as File|null,pan_document_path:null as File|null,kyc_document_path:null as File|null,_method:'put'});
const phoneForm=useForm({old_otp:'',new_otp:''}); const passwordForm=useForm({current_password:'',password:'',password_confirmation:'',_method:'put'});
const fileFrom=(event:Event)=>((event.target as HTMLInputElement).files||[])[0]||null;
const selectProfileImage=(event:Event)=>{profileForm.profile_image=fileFrom(event);if(profileForm.profile_image)profilePreview.value=URL.createObjectURL(profileForm.profile_image);};
const saveProfile=()=>profileForm.post(props.actions.update,{forceFormData:true,preserveScroll:true,onSuccess:()=>{profileSaved.value=true;window.setTimeout(()=>{profileSaved.value=false;},2200);}});
const sendOtp=async()=>{otpMessage.value='';otpError.value=false;if(!/^[0-9]{10}$/.test(newPhone.value)){otpMessage.value='Enter a valid 10-digit phone number.';otpError.value=true;return;}sendingOtp.value=true;try{const response=await axios.post(props.actions.sendOtp,{new_phone:newPhone.value});otpMessage.value=response.data.message;otpError.value=!response.data.success;otpSent.value=Boolean(response.data.success);}catch(error:any){otpMessage.value=error.response?.data?.message||'Unable to send OTP. Please try again.';otpError.value=true;}finally{sendingOtp.value=false;}};
const verifyPhone=()=>phoneForm.post(props.actions.verifyPhone,{preserveScroll:true}); const updatePassword=()=>passwordForm.post(props.actions.updatePassword,{preserveScroll:true,onSuccess:()=>passwordForm.reset()});
const Field=defineComponent({props:{label:{type:String,required:true},error:{type:String,default:''}},setup(p,{slots}){return()=>h('label',{class:'block'},[h('span',{class:'mb-2 block text-sm font-semibold text-slate-700'},p.label),slots.default?.(),p.error?h('p',{class:'mt-2 text-xs font-semibold text-red-600'},p.error):null]);}});
const DocStatus=defineComponent({props:{label:{type:String,required:true},ok:{type:Boolean,default:false}},setup(p){return()=>h('div',{class:'flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2'},[h('span',{class:'text-sm font-medium text-slate-700'},p.label),h('span',{class:['rounded-md px-2 py-0.5 text-xs font-semibold',p.ok?'bg-teal-50 text-teal-700':'bg-orange-50 text-orange-700']},p.ok?'Uploaded':'Missing')]);}});
</script>
