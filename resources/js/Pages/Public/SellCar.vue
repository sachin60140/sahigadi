<template>
    <Head>
        <title>Sell Your Car in Bihar | List Your Used Car - SahiGadi</title>
        <meta head-key="description" name="description" content="Sell your used car in Bihar with SahiGadi. Submit your car details, connect with verified buyers and dealers, and get genuine enquiries." />
        <link head-key="canonical" rel="canonical" href="https://sahigadi.com/sell-your-car" />
        <meta head-key="og-title" property="og:title" content="Sell Your Car in Bihar | List Your Used Car - SahiGadi" />
        <meta head-key="og-description" property="og:description" content="Sell your used car in Bihar with SahiGadi. Submit your car details, connect with verified buyers and dealers, and get genuine enquiries." />
        <meta head-key="og-url" property="og:url" content="https://sahigadi.com/sell-your-car" />
        <meta head-key="og-type" property="og:type" content="website" />
        <meta head-key="og-image" property="og:image" content="/images/og-image.png" />
        <meta head-key="keywords" name="keywords" content="sell used car Bihar, sell car online Patna, list second hand car, used car buyers Bihar" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
    </Head>

    <PublicLayout>
        <section class="relative overflow-hidden border-b border-slate-200 bg-[#f7fbff]">
            <div class="absolute inset-0">
                <img src="/images/hero-bg.png" alt="" class="h-full w-full object-cover opacity-20" />
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(247,251,255,0.96)_0%,rgba(238,251,248,0.93)_52%,rgba(255,247,237,0.94)_100%)]"></div>
            </div>
            <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                <div class="max-w-3xl">
                    <div class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg border border-orange-100 bg-white/85 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        SahiGadi seller desk
                    </div>
                    <h1 class="max-w-4xl text-3xl font-black leading-tight tracking-normal text-slate-950 sm:text-5xl lg:text-6xl">
                        List your car cleanly and reach serious local buyers.
                    </h1>
                    <p class="mt-5 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                        Add owner details, car specs, photos and expected price in one guided flow built for Bihar sellers.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <span v-for="item in heroBenefits" :key="item" class="inline-flex items-center gap-2 rounded-lg border border-white bg-white/80 px-3 py-2 text-sm font-black text-slate-700 shadow-sm">
                            <svg class="h-4 w-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ item }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div class="bg-[#f7fbff]">
        <div class="relative z-10 mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-[320px_minmax(0,1fr)] lg:px-8">
            <aside class="hidden lg:block">
                <div class="sticky top-24 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">How it works</p>
                    <h2 class="mt-2 text-xl font-black text-slate-950">Four simple steps to submit your listing.</h2>
                    <div class="mt-5 space-y-3">
                        <div v-for="(item, index) in processCards" :key="item.title" class="rounded-lg border border-slate-100 bg-[#f7fbff] p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-orange-600">Step {{ index + 1 }}</p>
                            <p class="mt-1 text-sm font-black text-slate-950">{{ item.title }}</p>
                            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">{{ item.text }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <SuccessState v-if="success" title="Car Details Submitted!" message="Your car details have been submitted. Our team will review and contact you soon." />

            <div v-else class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl shadow-slate-200/80">
                <div class="border-b border-slate-100 px-5 py-7 sm:px-8">
                    <p class="mb-4 text-sm font-black uppercase tracking-wide text-orange-600">Listing form</p>
                    <FormStepper :steps="steps" :currentStep="currentStep" />
                </div>

                <div class="px-5 py-8 sm:px-8">
                    <form @submit.prevent="submit">

                        <!-- Step 1: Owner Details -->
                        <div v-show="currentStep === 0" class="space-y-6">
                            <h3 class="mb-6 text-2xl font-black text-slate-950">Owner Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    id="owner_name"
                                    v-model="form.owner_name"
                                    label="Full Name"
                                    placeholder="Enter your name"
                                    :error="form.errors.owner_name"
                                />
                                <div>
                                    <FormInput
                                        id="owner_phone"
                                        v-model="form.owner_phone"
                                        label="Mobile Number"
                                        type="tel"
                                        placeholder="10-digit mobile number"
                                        :error="form.errors.owner_phone"
                                        required
                                        :maxlength="10"
                                        pattern="[0-9]{10}"
                                        :disabled="otpVerified || isAuthenticated"
                                    />

                                    <!-- OTP Verification Flow -->
                                    <div v-if="!isAuthenticated" class="mt-3">
                                        <!-- Error & Success Messages -->
                                        <p v-if="otpError" class="text-sm text-red-600 mb-2 font-medium">{{ otpError }}</p>
                                        <p v-if="otpSuccess" class="text-sm text-green-600 mb-2 font-medium flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            {{ otpSuccess }}
                                        </p>

                                        <!-- Action Buttons / Input -->
                                        <div v-if="otpVerified" class="inline-flex items-center rounded-lg border border-teal-200 bg-teal-50 px-3 py-1.5 text-xs font-black text-teal-700">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Verified
                                        </div>
                                        <div v-else-if="!otpSent">
                                            <button
                                                type="button"
                                                @click="sendOtp"
                                                :disabled="sendingOtp || form.owner_phone.length !== 10"
                                                class="text-sm font-black text-orange-600 hover:text-orange-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                {{ sendingOtp ? 'Sending...' : 'Send OTP to Verify' }}
                                            </button>
                                        </div>
                                        <div v-else class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:gap-3">
                                            <input
                                                type="text"
                                                v-model="otp"
                                                maxlength="6"
                                                placeholder="Enter 6-digit OTP"
                                                class="block w-full rounded-lg border-slate-300 px-4 py-2 shadow-sm focus:border-teal-700 focus:ring-teal-700 sm:w-32 sm:text-sm"
                                            />
                                            <button
                                                type="button"
                                                @click="verifyOtp"
                                                :disabled="verifyingOtp || otp.length !== 6"
                                                class="inline-flex items-center justify-center rounded-lg border border-transparent bg-slate-950 px-4 py-2 text-sm font-black text-white focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-70"
                                            >
                                                {{ verifyingOtp ? 'Verifying...' : 'Verify' }}
                                            </button>
                                            <button
                                                type="button"
                                                @click="sendOtp"
                                                :disabled="sendingOtp"
                                                class="text-center text-xs font-semibold text-gray-500 underline hover:text-gray-700 sm:ml-2"
                                            >
                                                Resend
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    id="whatsapp_number"
                                    v-model="form.whatsapp_number"
                                    label="WhatsApp Number (Optional)"
                                    type="tel"
                                    placeholder="10-digit mobile number"
                                    :error="form.errors.whatsapp_number"
                                    :maxlength="10"
                                    pattern="[0-9]{10}"
                                />
                                <FormInput
                                    id="owner_email"
                                    v-model="form.owner_email"
                                    label="Email Address (Optional)"
                                    type="email"
                                    placeholder="Enter your email"
                                    :error="form.errors.owner_email"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <FormInput
                                    id="city"
                                    v-model="form.city"
                                    label="City"
                                    placeholder="e.g. Patna"
                                    :error="form.errors.city"
                                />
                            </div>
                        </div>

                        <!-- Step 2: Car Details -->
                        <div v-show="currentStep === 1" class="space-y-6">
                            <h3 class="mb-6 text-2xl font-black text-slate-950">Car Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormSelect
                                    id="brand_id"
                                    v-model="form.brand_id"
                                    label="Brand"
                                    placeholder="Select Brand"
                                    :error="form.errors.brand_id"
                                    required
                                >
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                                </FormSelect>

                                <FormInput
                                    id="model"
                                    v-model="form.model"
                                    label="Model & Variant"
                                    placeholder="e.g. Swift Dzire VXI"
                                    :error="form.errors.model"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <FormInput
                                    id="year"
                                    v-model="form.year"
                                    label="Manufacturing Year"
                                    type="number"
                                    placeholder="e.g. 2019"
                                    :error="form.errors.year"
                                />
                                <FormSelect
                                    id="fuel_type"
                                    v-model="form.fuel_type"
                                    label="Fuel Type"
                                    placeholder="Select Fuel"
                                    :error="form.errors.fuel_type"
                                >
                                    <option v-for="(label, value) in fuelTypes" :key="value" :value="value">{{ label }}</option>
                                </FormSelect>
                                <FormSelect
                                    id="transmission"
                                    v-model="form.transmission"
                                    label="Transmission"
                                    placeholder="Select Transmission"
                                    :error="form.errors.transmission"
                                >
                                    <option v-for="(label, value) in transmissions" :key="value" :value="value">{{ label }}</option>
                                </FormSelect>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    id="km_driven"
                                    v-model="form.km_driven"
                                    label="KM Driven"
                                    type="number"
                                    placeholder="e.g. 45000"
                                    :error="form.errors.km_driven"
                                />
                                <FormInput
                                    id="owners"
                                    v-model="form.owners"
                                    label="No. of Owners"
                                    type="number"
                                    placeholder="e.g. 1"
                                    :error="form.errors.owners"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    id="registration_number"
                                    v-model="form.registration_number"
                                    label="Registration Number"
                                    placeholder="e.g. BR01BX1234"
                                    :error="form.errors.registration_number"
                                />
                                <FormInput
                                    id="price"
                                    v-model="form.price"
                                    label="Expected Price (₹)"
                                    type="number"
                                    placeholder="e.g. 450000"
                                    :error="form.errors.price"
                                />
                            </div>
                        </div>

                        <!-- Step 3: Condition & Photos -->
                        <div v-show="currentStep === 2" class="space-y-6">
                            <h3 class="mb-2 text-2xl font-black text-slate-950">Condition & Photos</h3>
                            <p class="mb-6 text-sm font-medium text-slate-500">Upload at least 5 clear photos of your car. Location access is required to list a vehicle.</p>

                            <FileUploadPreview
                                id="images"
                                v-model="form.images"
                                v-model:primaryIndex="form.primary_image_index"
                                :error="form.errors['images'] || form.errors['images.0']"
                                :maxFiles="10"
                            />

                            <div v-if="!locationFetched" class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-yellow-400 mt-0.5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-bold text-yellow-800">Location Required</h4>
                                        <p class="mt-1 text-sm text-yellow-700">We need your location to securely list your car. Please allow access.</p>
                                        <button type="button" @click="fetchLocation" class="mt-2 text-sm font-semibold text-yellow-800 underline">Fetch Location Now</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.errors.latitude" class="mt-2 text-sm text-red-600 font-medium">
                                {{ form.errors.latitude }}
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div v-show="currentStep === 3" class="space-y-6">
                            <h3 class="mb-6 text-2xl font-black text-slate-950">Review & Submit</h3>

                            <div class="space-y-4 rounded-lg border border-slate-200 bg-slate-50 p-5 sm:p-6">
                                <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                                    <div>
                                        <span class="block text-gray-500 font-medium">Full Name</span>
                                        <span class="block font-bold text-gray-900">{{ form.owner_name || '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 font-medium">Mobile Number</span>
                                        <span class="block font-bold text-gray-900">{{ form.owner_phone }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-4 sm:col-span-2">
                                        <span class="block text-gray-500 font-medium">Car</span>
                                        <span class="block font-bold text-gray-900">{{ getBrandName(form.brand_id) }} {{ form.model }} ({{ form.year }})</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 font-medium">Expected Price</span>
                                        <span class="block font-black text-orange-600">₹{{ form.price }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 font-medium">Photos Added</span>
                                        <span class="block font-bold text-gray-900">{{ form.images.length }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="consent" v-model="consent" type="checkbox" class="h-4 w-4 rounded border-slate-300 bg-slate-100 text-teal-700 focus:ring-2 focus:ring-teal-700">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="consent" class="font-medium text-gray-700">I confirm that the information provided is correct and I have the authority to sell this vehicle.</label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-10 flex flex-col items-stretch gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                            <button
                                type="button"
                                v-if="currentStep > 0"
                                @click="prevStep"
                                class="w-full rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 sm:w-auto"
                            >
                                Back
                            </button>
                            <div v-else class="hidden sm:block"></div>

                            <button
                                type="button"
                                v-if="currentStep < steps.length - 1"
                                @click="nextStep"
                                class="w-full rounded-lg border border-transparent bg-slate-950 px-8 py-3 text-sm font-black text-white shadow-sm transition-colors hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 sm:w-auto"
                            >
                                Continue
                            </button>

                            <button
                                type="submit"
                                v-if="currentStep === steps.length - 1"
                                :disabled="form.processing || !consent"
                                class="flex w-full items-center justify-center rounded-lg border border-transparent bg-orange-500 px-8 py-3 text-sm font-black text-white shadow-sm transition-colors hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Submitting...' : 'Submit Car for Review' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import FormStepper from '@/Components/Public/FormStepper.vue';
import FormInput from '@/Components/Public/FormInput.vue';
import FormSelect from '@/Components/Public/FormSelect.vue';
import FileUploadPreview from '@/Components/Public/FileUploadPreview.vue';
import SuccessState from '@/Components/Public/SuccessState.vue';

const props = defineProps<{
    brands: Array<{ id: number, name: string }>;
    fuelTypes: Record<string, string>;
    transmissions: Record<string, string>;
}>();

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const steps = [
    { title: 'Owner' },
    { title: 'Car' },
    { title: 'Photos' },
    { title: 'Review' }
];

const heroBenefits = [
    'Free listing support',
    'Verified buyer enquiries',
    'OTP-protected contact',
];

const processCards = [
    {
        title: 'Verify owner contact',
        text: 'Confirm your mobile number before sharing listing details.',
    },
    {
        title: 'Add vehicle specs',
        text: 'Enter brand, model, year, fuel, kilometres and price.',
    },
    {
        title: 'Upload clear photos',
        text: 'Add multiple angles so buyers can judge condition quickly.',
    },
    {
        title: 'Review and submit',
        text: 'Check the listing once before sending it for review.',
    },
];

const currentStep = ref(0);
const success = ref(false);
const consent = ref(false);
const locationFetched = ref(false);

const form = useForm({
    title: '',
    brand_id: '',
    model: '',
    year: '',
    fuel_type: '',
    transmission: '',
    km_driven: '',
    price: '',
    city: '',
    registration_number: '',
    owners: '',
    owner_name: '',
    owner_email: '',
    owner_phone: '',
    whatsapp_number: '',
    latitude: '' as string | number,
    longitude: '' as string | number,
    images: [] as File[],
    primary_image_index: 0,
});

// OTP State
const otp = ref('');
const otpSent = ref(false);
const otpVerified = ref(false);
const sendingOtp = ref(false);
const verifyingOtp = ref(false);
const otpError = ref('');
const otpSuccess = ref('');

// Reset OTP if phone changes
watch(() => form.owner_phone, () => {
    if (otpVerified.value) {
        otpVerified.value = false;
        otpSent.value = false;
        otp.value = '';
    }
});

const sendOtp = async () => {
    if (!form.owner_phone || form.owner_phone.length !== 10) {
        form.setError('owner_phone', 'Enter a valid 10-digit mobile number');
        return;
    }

    sendingOtp.value = true;
    otpError.value = '';
    otpSuccess.value = '';
    form.clearErrors('owner_phone');

    try {
        const response = await axios.post('/sell-your-car/send-otp', {
            phone: form.owner_phone
        });

        if (response.data.success) {
            otpSent.value = true;
            otpSuccess.value = 'OTP sent successfully! Check your SMS.';
        } else {
            otpError.value = response.data.message || 'Failed to send OTP';
        }
    } catch (error: any) {
        otpError.value = error.response?.data?.message || 'Failed to send OTP. Please try again.';
    } finally {
        sendingOtp.value = false;
    }
};

const verifyOtp = async () => {
    if (!otp.value || otp.value.length !== 6) {
        otpError.value = 'Enter a valid 6-digit OTP';
        return;
    }

    verifyingOtp.value = true;
    otpError.value = '';
    otpSuccess.value = '';

    try {
        const response = await axios.post('/sell-your-car/verify-otp', {
            phone: form.owner_phone,
            otp: otp.value
        });

        if (response.data.success) {
            otpVerified.value = true;
            otpSuccess.value = 'Mobile number verified successfully!';
        } else {
            otpError.value = response.data.message || 'Invalid OTP';
        }
    } catch (error: any) {
        otpError.value = error.response?.data?.message || 'Failed to verify OTP. Please try again.';
    } finally {
        verifyingOtp.value = false;
    }
};

const getBrandName = (id: string | number) => {
    const brand = props.brands.find(b => b.id == id);
    return brand ? brand.name : '';
};

const nextStep = () => {
    // Basic frontend validation before advancing
    if (currentStep.value === 0) {
        if (!form.owner_phone) return form.setError('owner_phone', 'Mobile number is required');
        if (!isAuthenticated.value && !otpVerified.value) return form.setError('owner_phone', 'Please verify your mobile number with OTP');
        form.clearErrors();
    } else if (currentStep.value === 1) {
        if (!form.brand_id) return form.setError('brand_id', 'Brand is required');
        form.clearErrors();
    } else if (currentStep.value === 2) {
        if (form.images.length < 5) return form.setError('images', 'Please upload at least 5 images');
        form.clearErrors();
    }

    currentStep.value++;
    window.scrollTo({ top: 300, behavior: 'smooth' });
};

const prevStep = () => {
    currentStep.value--;
    window.scrollTo({ top: 300, behavior: 'smooth' });
};

const fetchLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude = position.coords.latitude;
                form.longitude = position.coords.longitude;
                locationFetched.value = true;
                form.clearErrors('latitude', 'longitude');
            },
            (error) => {
                form.setError('latitude', 'Could not get location. Please allow location access.');
            }
        );
    } else {
        form.setError('latitude', 'Geolocation is not supported by your browser.');
    }
};

const submit = () => {
    // Construct dynamic title before submit
    if (form.brand_id && form.model) {
        form.title = `${getBrandName(form.brand_id)} ${form.model} ${form.year || ''}`.trim();
    }

    form.post('/sell-your-car', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            success.value = true;
        },
        onError: (errors) => {
            // If error is related to a specific step, go back to that step
            if (errors.owner_phone || errors.owner_name) currentStep.value = 0;
            else if (errors.brand_id || errors.model || errors.price) currentStep.value = 1;
            else if (errors.images || errors.latitude) currentStep.value = 2;
        }
    });
};

onMounted(() => {
    fetchLocation();
});
</script>
