<template>
    <Head title="My Profile" />
    <DealerLayout title="My Profile" eyebrow="Account and verification">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="h-28 bg-slate-950 sm:h-36"></div>
            <div class="px-5 pb-5 sm:px-6">
                <div class="-mt-12 flex flex-col gap-4 sm:-mt-14 sm:flex-row sm:items-end">
                    <div class="relative h-28 w-28 shrink-0 rounded-lg border-4 border-white bg-slate-100 shadow-lg sm:h-32 sm:w-32">
                        <img :src="profilePreview" alt="Dealer profile" class="h-full w-full rounded-md object-cover" />
                    </div>
                    <div class="min-w-0 pb-1"><h2 class="truncate text-2xl font-semibold text-slate-950">{{ dealer.name }}</h2><p class="mt-1 text-sm font-bold text-slate-500">{{ dealer.company_name || 'Independent dealer' }} / {{ dealer.dealer_unique_id }}</p></div>
                </div>
            </div>
        </section>

        <div class="mt-5 flex overflow-x-auto" role="tablist" aria-label="Profile sections">
            <button v-for="item in tabs" :key="item.value" type="button" class="min-w-max border-b-2 px-4 py-3 text-sm font-semibold" :class="tab===item.value?'border-teal-700 text-teal-700':'border-transparent text-slate-500'" @click="tab=item.value">{{ item.label }}</button>
        </div>

        <section v-if="tab==='profile'" class="mt-5 grid gap-5 xl:grid-cols-[300px_minmax(0,1fr)]">
            <aside class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3"><p class="text-sm font-semibold text-slate-950">Profile completion</p><span class="rounded-md bg-teal-50 px-2.5 py-1 text-sm font-semibold text-teal-700">{{ dealer.completion }}%</span></div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-teal-700" :style="{width:`${dealer.completion}%`}"></div></div>
                <div v-if="dealer.missing_fields.length" class="mt-5"><p class="text-xs font-semibold uppercase tracking-wide text-orange-700">Still needed</p><div class="mt-3 flex flex-wrap gap-2"><span v-for="field in dealer.missing_fields" :key="field" class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700">{{ field }}</span></div></div>
            </aside>

            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="saveProfile">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Business identity</p>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <Field label="Full name" :error="profileForm.errors.name"><input v-model="profileForm.name" class="dealer-input" required /></Field>
                    <Field label="Company name" :error="profileForm.errors.company_name"><input v-model="profileForm.company_name" class="dealer-input" /></Field>
                    <Field label="Email address"><input :value="dealer.email" class="dealer-input bg-slate-50" disabled /><p class="mt-2 text-xs font-bold text-slate-500">Email cannot be changed here.</p></Field>
                    <Field label="Profile photo" :error="profileForm.errors.profile_image"><input class="dealer-input file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold" type="file" accept="image/*" @change="selectProfileImage" /></Field>
                    <Field label="PAN number" :error="profileForm.errors.pan_number"><input v-model="profileForm.pan_number" class="dealer-input uppercase" maxlength="20" /></Field>
                    <Field label="PAN document" :error="profileForm.errors.pan_document_path"><input class="dealer-input" type="file" accept=".jpg,.jpeg,.png,.pdf" @change="profileForm.pan_document_path=fileFrom($event)" /><a v-if="dealer.pan_document_url" :href="dealer.pan_document_url" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-xs font-semibold text-teal-700">View current document</a></Field>
                    <Field label="KYC document type" :error="profileForm.errors.kyc_document_type"><select v-model="profileForm.kyc_document_type" class="dealer-input"><option value="">Select type</option><option>Aadhaar</option><option>Voter ID</option><option>Passport</option><option>Driving License</option></select></Field>
                    <Field label="KYC document number" :error="profileForm.errors.kyc_document_number"><input v-model="profileForm.kyc_document_number" class="dealer-input" /></Field>
                    <Field label="KYC document" :error="profileForm.errors.kyc_document_path"><input class="dealer-input" type="file" accept=".jpg,.jpeg,.png,.pdf" @change="profileForm.kyc_document_path=fileFrom($event)" /><a v-if="dealer.kyc_document_url" :href="dealer.kyc_document_url" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-xs font-semibold text-teal-700">View current document</a></Field>
                </div>
                <p class="mt-8 text-xs font-semibold uppercase tracking-wide text-teal-700">Location</p>
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <Field class="md:col-span-3" label="Full address" :error="profileForm.errors.address"><input v-model="profileForm.address" class="dealer-input" /></Field>
                    <Field label="City" :error="profileForm.errors.city"><input v-model="profileForm.city" class="dealer-input" /></Field>
                    <Field label="State" :error="profileForm.errors.state"><input v-model="profileForm.state" class="dealer-input" /></Field>
                    <Field label="Pincode" :error="profileForm.errors.pincode"><input v-model="profileForm.pincode" class="dealer-input" /></Field>
                </div>
                <button class="mt-6 rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-800 disabled:opacity-60" :disabled="profileForm.processing">{{ profileForm.processing?'Saving...':'Save profile' }}</button>
            </form>
        </section>

        <section v-else class="mt-5 grid gap-5 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Mobile verification</p><h2 class="mt-2 text-xl font-semibold text-slate-950">Update registered phone</h2><p class="mt-2 text-sm font-semibold text-slate-600">Current number: {{ dealer.phone }}</p>
                <div class="mt-5 flex flex-col gap-2 sm:flex-row"><input v-model="newPhone" class="dealer-input" maxlength="10" inputmode="numeric" placeholder="New 10-digit number" :readonly="otpSent" /><button v-if="!otpSent" type="button" class="min-h-12 shrink-0 rounded-lg bg-slate-950 px-5 text-sm font-semibold text-white disabled:opacity-50" :disabled="sendingOtp" @click="sendOtp">{{ sendingOtp?'Sending...':'Request OTP' }}</button></div>
                <p v-if="otpMessage" class="mt-3 text-sm font-bold" :class="otpError?'text-red-600':'text-teal-700'">{{ otpMessage }}</p>
                <form v-if="otpSent" class="mt-5 grid gap-4 sm:grid-cols-2" @submit.prevent="verifyPhone">
                    <Field label="OTP on current phone" :error="phoneForm.errors.old_otp"><input v-model="phoneForm.old_otp" class="dealer-input text-center" maxlength="6" inputmode="numeric" required /></Field>
                    <Field label="OTP on new phone" :error="phoneForm.errors.new_otp"><input v-model="phoneForm.new_otp" class="dealer-input text-center" maxlength="6" inputmode="numeric" required /></Field>
                    <button class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white sm:col-span-2" :disabled="phoneForm.processing">Verify and update phone</button>
                </form>
            </div>

            <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" @submit.prevent="updatePassword">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Account security</p><h2 class="mt-2 text-xl font-semibold text-slate-950">Change password</h2>
                <div class="mt-5 grid gap-4">
                    <Field label="Current password" :error="passwordForm.errors.current_password"><input v-model="passwordForm.current_password" class="dealer-input" type="password" required /></Field>
                    <Field label="New password" :error="passwordForm.errors.password"><input v-model="passwordForm.password" class="dealer-input" type="password" minlength="8" required /></Field>
                    <Field label="Confirm new password"><input v-model="passwordForm.password_confirmation" class="dealer-input" type="password" minlength="8" required /></Field>
                </div>
                <button class="mt-5 rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 disabled:opacity-60" :disabled="passwordForm.processing">Update password</button>
            </form>
        </section>
    </DealerLayout>
</template>

<script setup lang="ts">
import { defineComponent, h, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import DealerLayout from '@/Layouts/DealerLayout.vue';

const props=defineProps<{dealer:any;actions:{update:string;sendOtp:string;verifyPhone:string;updatePassword:string}}>();
const tab=ref('profile'); const tabs=[{value:'profile',label:'Profile and KYC'},{value:'security',label:'Security and phone'}];
const profilePreview=ref(props.dealer.profile_image_url); const newPhone=ref(''); const otpSent=ref(false); const sendingOtp=ref(false); const otpMessage=ref(''); const otpError=ref(false);
const profileForm=useForm({name:props.dealer.name,company_name:props.dealer.company_name||'',address:props.dealer.address||'',city:props.dealer.city||'',state:props.dealer.state||'',pincode:props.dealer.pincode||'',pan_number:props.dealer.pan_number||'',kyc_document_type:props.dealer.kyc_document_type||'',kyc_document_number:props.dealer.kyc_document_number||'',profile_image:null as File|null,pan_document_path:null as File|null,kyc_document_path:null as File|null,_method:'put'});
const phoneForm=useForm({old_otp:'',new_otp:''}); const passwordForm=useForm({current_password:'',password:'',password_confirmation:'',_method:'put'});
const fileFrom=(event:Event)=>((event.target as HTMLInputElement).files||[])[0]||null;
const selectProfileImage=(event:Event)=>{profileForm.profile_image=fileFrom(event);if(profileForm.profile_image)profilePreview.value=URL.createObjectURL(profileForm.profile_image);};
const saveProfile=()=>profileForm.post(props.actions.update,{forceFormData:true,preserveScroll:true});
const sendOtp=async()=>{otpMessage.value='';otpError.value=false;if(!/^[0-9]{10}$/.test(newPhone.value)){otpMessage.value='Enter a valid 10-digit phone number.';otpError.value=true;return;}sendingOtp.value=true;try{const response=await axios.post(props.actions.sendOtp,{new_phone:newPhone.value});otpMessage.value=response.data.message;otpError.value=!response.data.success;otpSent.value=Boolean(response.data.success);}catch(error:any){otpMessage.value=error.response?.data?.message||'Unable to send OTP. Please try again.';otpError.value=true;}finally{sendingOtp.value=false;}};
const verifyPhone=()=>phoneForm.post(props.actions.verifyPhone,{preserveScroll:true}); const updatePassword=()=>passwordForm.post(props.actions.updatePassword,{preserveScroll:true,onSuccess:()=>passwordForm.reset()});
const Field=defineComponent({props:{label:{type:String,required:true},error:{type:String,default:''}},setup(p,{slots}){return()=>h('label',[h('span',{class:'mb-2 block text-sm font-semibold text-slate-700'},p.label),slots.default?.(),p.error?h('p',{class:'mt-2 text-xs font-bold text-red-600'},p.error):null]);}});
</script>
