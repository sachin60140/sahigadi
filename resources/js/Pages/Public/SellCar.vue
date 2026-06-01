<template>
    <Head>
        <title>Sell Your Car - Get the Best Price | SahiGadi</title>
        <meta name="description" content="Sell your used car quickly and get the best market price in Bihar. Free evaluation and instant payment." />
    </Head>

    <PublicLayout>
        <div class="bg-gray-50 min-h-screen py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gray-900">Sell Your Car</h1>
                    <p class="mt-2 text-gray-600">Fill out the details below to list your car on SahiGadi.</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -mt-px w-full h-0.5 bg-gray-200" aria-hidden="true"></div>
                        
                        <div v-for="s in 4" :key="s" class="relative flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors duration-300 z-10"
                                 :class="step >= s ? 'bg-blue-600 text-white shadow-md ring-4 ring-white' : 'bg-white border-2 border-gray-300 text-gray-500 ring-4 ring-white'">
                                <span v-if="step > s">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <span v-else>{{ s }}</span>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-500 hidden sm:block">
                                {{ s === 1 ? 'Car Details' : s === 2 ? 'Location & Price' : s === 3 ? 'Photos' : 'Verification' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <form @submit.prevent="submitForm" class="p-6 md:p-8">
                        
                        <!-- Step 1: Car Details -->
                        <div v-show="step === 1" class="space-y-6 animate-fade-in">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Basic Information</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ad Title <span class="text-red-500">*</span></label>
                                    <input v-model="form.title" type="text" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Maruti Suzuki Swift VXI 2019" />
                                    <p class="text-xs text-gray-500 mt-1" v-if="form.errors.title">{{ form.errors.title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand <span class="text-red-500">*</span></label>
                                    <select v-model="form.brand_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Brand</option>
                                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1" v-if="form.errors.brand_id">{{ form.errors.brand_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Model / Variant <span class="text-red-500">*</span></label>
                                    <input v-model="form.model" type="text" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Swift VXI" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Year <span class="text-red-500">*</span></label>
                                    <select v-model="form.year" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Year</option>
                                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">KMs Driven <span class="text-red-500">*</span></label>
                                    <input v-model="form.km_driven" type="number" required min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 45000" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type <span class="text-red-500">*</span></label>
                                    <select v-model="form.fuel_type" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Fuel</option>
                                        <option v-for="(label, val) in fuelTypes" :key="val" :value="val">{{ label }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Transmission <span class="text-red-500">*</span></label>
                                    <select v-model="form.transmission" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Transmission</option>
                                        <option v-for="(label, val) in transmissions" :key="val" :value="val">{{ label }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Owners <span class="text-red-500">*</span></label>
                                    <select v-model="form.owners" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="1">1st Owner</option>
                                        <option value="2">2nd Owner</option>
                                        <option value="3">3rd Owner</option>
                                        <option value="4">4th+ Owner</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input v-model="form.registration_number" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase" placeholder="e.g. BR06AB1234" />
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-end">
                                <button type="button" @click="nextStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg transition flex items-center">
                                    Next Step
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Location & Price -->
                        <div v-show="step === 2" class="space-y-6 animate-fade-in">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Location & Pricing</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Asking Price (₹) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-semibold">₹</span>
                                        <input v-model="form.price" type="number" required min="10000" class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3 font-bold" placeholder="e.g. 450000" />
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" v-if="form.errors.price">{{ form.errors.price }}</p>
                                </div>

                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                                    <input v-model="form.city" type="text" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Muzaffarpur" />
                                </div>

                                <div class="col-span-full bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900">Pinpoint Location</h4>
                                            <p class="text-sm text-gray-600 mb-3 mt-1">We need your location coordinates to help buyers find your car. Click the button below to auto-fetch.</p>
                                            
                                            <button type="button" @click="getLocation" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded shadow-sm text-sm transition">
                                                {{ locationStatus || 'Detect My Location' }}
                                            </button>
                                            
                                            <div v-if="form.latitude && form.longitude" class="mt-3 text-xs font-mono text-green-700 bg-green-100 px-3 py-1.5 rounded inline-block">
                                                Lat: {{ parseFloat(form.latitude).toFixed(4) }}, Lng: {{ parseFloat(form.longitude).toFixed(4) }}
                                            </div>
                                            <p class="text-xs text-red-500 mt-2" v-if="form.errors.latitude">{{ form.errors.latitude }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-between">
                                <button type="button" @click="step = 1" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition">Back</button>
                                <button type="button" @click="nextStep(3)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg transition flex items-center">
                                    Next Step
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Photos -->
                        <div v-show="step === 3" class="space-y-6 animate-fade-in">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Upload Photos</h2>
                            <p class="text-sm text-gray-600 mb-4">A good ad needs good photos. Upload at least 5 clear images (Max 10). The first image will be the primary photo.</p>
                            
                            <div class="border-2 border-dashed border-blue-300 bg-blue-50 rounded-xl p-8 text-center transition-colors hover:bg-blue-100 cursor-pointer relative"
                                 @dragover.prevent @drop.prevent="handleDrop" @click="$refs.fileInput.click()">
                                <input type="file" multiple accept="image/*" class="hidden" ref="fileInput" @change="handleFileSelect" />
                                
                                <svg class="w-12 h-12 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-gray-700 font-medium text-lg">Click or Drag & Drop photos here</p>
                                <p class="text-gray-500 text-sm mt-1">JPEG, PNG up to 2MB each</p>
                            </div>

                            <p class="text-xs text-red-500 font-medium" v-if="form.errors.images || imageError">{{ form.errors.images || imageError }}</p>

                            <div v-if="previewImages.length > 0" class="mt-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Selected Photos ({{ previewImages.length }}/10)</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                                    <div v-for="(preview, index) in previewImages" :key="index" class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-[4/3]">
                                        <img :src="preview.url" class="w-full h-full object-cover" />
                                        
                                        <!-- Primary Badge -->
                                        <div v-if="index === form.primary_image_index" class="absolute top-1 left-1 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                            Main
                                        </div>
                                        
                                        <!-- Actions overlay -->
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                                            <button type="button" @click.stop="setPrimary(index)" class="bg-white text-gray-900 text-xs font-bold px-2 py-1 rounded hover:bg-gray-100" v-if="index !== form.primary_image_index">
                                                Set Main
                                            </button>
                                            <button type="button" @click.stop="removeImage(index)" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded hover:bg-red-600">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-between">
                                <button type="button" @click="step = 2" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition">Back</button>
                                <button type="button" @click="nextStep(4)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg transition flex items-center">
                                    Next Step
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Contact & Submit -->
                        <div v-show="step === 4" class="space-y-6 animate-fade-in">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-2">Seller Details</h2>
                            
                            <!-- If already logged in, show a simplified message -->
                            <div v-if="$page.props.auth?.customer" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start">
                                <svg class="w-6 h-6 text-green-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h4 class="text-sm font-bold text-green-900">Authenticated</h4>
                                    <p class="text-sm text-green-700 mt-1">You are logged in as <strong>{{ $page.props.auth.customer.name }}</strong> ({{ $page.props.auth.customer.phone }}). Your listing will be attached to your account.</p>
                                </div>
                            </div>
                            
                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                                    <input v-model="form.owner_name" type="text" :disabled="isPhoneVerified" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input v-model="form.owner_email" type="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                
                                <div class="col-span-full">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                                    <div class="flex gap-4 items-start">
                                        <div class="relative flex-grow">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 border-r border-gray-300 pr-2">+91</span>
                                            <input v-model="form.owner_phone" type="tel" pattern="[0-9]{10}" :disabled="isPhoneVerified" required class="w-full pl-14 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 disabled:bg-gray-100 disabled:text-gray-500 font-bold" />
                                        </div>
                                        <button v-if="!isPhoneVerified" type="button" @click="sendOtp" :disabled="isLoadingOtp || !isPhoneValid" class="bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold py-2.5 px-6 rounded-lg transition disabled:opacity-50 shrink-0 border border-blue-200">
                                            {{ isLoadingOtp ? 'Sending...' : 'Verify' }}
                                        </button>
                                        <div v-else class="bg-green-100 text-green-700 font-bold py-2.5 px-6 rounded-lg shrink-0 flex items-center border border-green-200">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Verified
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-500 mt-1" v-if="otpError">{{ otpError }}</p>
                                </div>

                                <!-- OTP Verification UI -->
                                <div v-if="showOtpInput && !isPhoneVerified" class="col-span-full bg-blue-50 border border-blue-100 rounded-xl p-6">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Enter 6-digit OTP sent to your phone</label>
                                    <div class="flex gap-4">
                                        <input v-model="otpInput" type="text" pattern="[0-9]{6}" maxlength="6" class="flex-grow border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center tracking-[0.5em] font-bold text-xl" placeholder="------" />
                                        <button type="button" @click="verifyOtp" :disabled="isLoadingOtp || otpInput.length !== 6" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition disabled:opacity-50">
                                            Confirm
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                            </div>

                            <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-100">
                                <button type="button" @click="step = 3" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition">Back</button>
                                <button type="submit" :disabled="form.processing || (!isPhoneVerified && !$page.props.auth?.customer)" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-10 rounded-lg transition shadow-md flex items-center disabled:opacity-50 text-lg">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Submit Listing
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import axios from 'axios';

const props = defineProps<{
    brands: any[];
    fuelTypes: Record<string, string>;
    transmissions: Record<string, string>;
}>();

const page = usePage();
const step = ref(1);

// Generate array of years
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 25 }, (_, i) => currentYear - i);

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
    latitude: '',
    longitude: '',
    registration_number: '',
    owners: '1',
    owner_name: '',
    owner_phone: '',
    owner_email: '',
    images: [] as File[],
    primary_image_index: 0,
});

const nextStep = (target: number) => {
    // Basic validation before proceeding
    if (target === 2) {
        if (!form.title || !form.brand_id || !form.model || !form.year || !form.fuel_type || !form.transmission || !form.km_driven) {
            alert('Please fill all required fields marked with *');
            return;
        }
    }
    if (target === 3) {
        if (!form.price || !form.city) {
            alert('Please fill price and city');
            return;
        }
        if (!form.latitude || !form.longitude) {
            alert('Please pinpoint your location');
            return;
        }
    }
    if (target === 4) {
        if (form.images.length < 5) {
            imageError.value = 'Minimum 5 photos required';
            return;
        }
    }
    step.value = target;
};

// --- Location Logic ---
const locationStatus = ref('');
const getLocation = () => {
    locationStatus.value = 'Detecting...';
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude = position.coords.latitude.toString();
                form.longitude = position.coords.longitude.toString();
                locationStatus.value = 'Location Detected \u2713';
            },
            (error) => {
                locationStatus.value = 'Failed to detect';
                alert('Could not detect location. Please enable location services in your browser.');
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
};

// --- Photo Upload Logic ---
const previewImages = ref<{file: File, url: string}[]>([]);
const imageError = ref('');

const processFiles = (files: FileList | File[]) => {
    imageError.value = '';
    const newFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
    
    if (previewImages.value.length + newFiles.length > 10) {
        imageError.value = 'Maximum 10 photos allowed.';
        return;
    }
    
    newFiles.forEach(file => {
        if (file.size > 5 * 1024 * 1024) { // 5MB limit client side just in case
            imageError.value = 'Each file must be less than 5MB';
            return;
        }
        previewImages.value.push({
            file,
            url: URL.createObjectURL(file)
        });
    });
    
    syncFormImages();
};

const handleFileSelect = (event: any) => {
    if (event.target.files) {
        processFiles(event.target.files);
    }
    // reset input
    event.target.value = '';
};

const handleDrop = (event: any) => {
    if (event.dataTransfer.files) {
        processFiles(event.dataTransfer.files);
    }
};

const removeImage = (index: number) => {
    URL.revokeObjectURL(previewImages.value[index].url);
    previewImages.value.splice(index, 1);
    if (form.primary_image_index === index) {
        form.primary_image_index = 0;
    } else if (form.primary_image_index > index) {
        form.primary_image_index--;
    }
    syncFormImages();
};

const setPrimary = (index: number) => {
    form.primary_image_index = index;
};

const syncFormImages = () => {
    form.images = previewImages.value.map(p => p.file);
};


// --- OTP Logic ---
const isPhoneVerified = ref(false);
const showOtpInput = ref(false);
const isLoadingOtp = ref(false);
const otpInput = ref('');
const otpError = ref('');

const isPhoneValid = computed(() => {
    return form.owner_phone && form.owner_phone.length === 10 && /^\d+$/.test(form.owner_phone);
});

const sendOtp = async () => {
    otpError.value = '';
    isLoadingOtp.value = true;
    
    try {
        const res = await axios.post('/sell-your-car/send-otp', { phone: form.owner_phone });
        if (res.data.success) {
            showOtpInput.value = true;
        } else {
            otpError.value = res.data.message || 'Error sending OTP';
        }
    } catch(e: any) {
        otpError.value = e.response?.data?.message || 'Server error';
    } finally {
        isLoadingOtp.value = false;
    }
};

const verifyOtp = async () => {
    otpError.value = '';
    isLoadingOtp.value = true;
    
    try {
        const res = await axios.post('/sell-your-car/verify-otp', { 
            phone: form.owner_phone,
            otp: otpInput.value 
        });
        if (res.data.success) {
            isPhoneVerified.value = true;
            showOtpInput.value = false;
        } else {
            otpError.value = res.data.message || 'Invalid OTP';
        }
    } catch(e: any) {
        otpError.value = e.response?.data?.message || 'Server error';
    } finally {
        isLoadingOtp.value = false;
    }
};

// --- Form Submit ---
const submitForm = () => {
    form.post('/sell-your-car', {
        preserveScroll: true,
        onSuccess: () => {
            // Success handles redirection back with success flash, handled in layout ideally
            alert('Your car listing has been submitted successfully! We will review it and get back to you soon.');
            window.location.href = '/';
        }
    });
};
</script>
