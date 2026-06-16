<template>
    <Head title="Dealer Registration | SahiGadi" />
    
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2 bg-gray-50">
        <!-- Left Panel: Trust & Benefits (Hidden on mobile) -->
        <aside class="hidden lg:flex relative overflow-hidden bg-[#0f172a] text-white p-12 flex-col justify-between">
            <div class="absolute inset-0 opacity-10 "></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#f97316] rounded-full filter blur-[120px] opacity-20"></div>
            
            <div class="relative z-10">
                <Link href="/" class="flex mb-16">
                    <span class="text-4xl font-semibold tracking-tight text-white">SAHI<span class="text-[#f97316]">GADI</span></span>
                </Link>
                
                <h1 class="text-4xl font-extrabold leading-tight mb-6">
                    Grow your used car business with SahiGadi
                </h1>
                
                <p class="text-xl text-gray-300 mb-12 max-w-lg">
                    Join Bihar's fastest-growing premium used car marketplace and connect with thousands of verified buyers daily.
                </p>
                
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-white/10 text-[#f97316]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold">Verified Leads</h3>
                            <p class="mt-1 text-gray-400">Get OTP-verified genuine buyer enquiries directly to your dashboard.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-white/10 text-[#f97316]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold">Digital Showroom</h3>
                            <p class="mt-1 text-gray-400">Manage your entire inventory online with our powerful dealer tools.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-white/10 text-[#f97316]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold">Build Trust</h3>
                            <p class="mt-1 text-gray-400">Get a "Verified Dealer" badge and increase your sales conversions.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative z-10 text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} SahiGadi.com. All rights reserved.
            </div>
        </aside>
        
        <!-- Right Panel: Registration Form -->
        <main class="flex items-center justify-center p-4 sm:p-8">
            <div class="w-full max-w-md lg:max-w-xl">
                <!-- Mobile Logo -->
                <Link href="/" class="flex lg:hidden mb-8">
                    <span class="text-3xl font-semibold text-[#0f172a] tracking-tight">SAHI<span class="text-[#f97316]">GADI</span></span>
                </Link>
                
                <h2 class="text-3xl font-extrabold text-[#0f172a] mb-2">Dealer Registration</h2>
                <p class="text-gray-500 mb-8">Create your account to start listing cars today.</p>
                
                <SuccessState v-if="success" title="Application Submitted" message="Your dealer registration has been submitted successfully. Our team will verify your details and approve your account shortly." />
                
                <form v-else @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormInput
                            id="business_name"
                            v-model="form.business_name"
                            label="Dealership Name"
                            placeholder="e.g. Sharma Motors"
                            :error="form.errors.business_name"
                            required
                        />
                        <FormInput
                            id="name"
                            v-model="form.name"
                            label="Owner Full Name"
                            placeholder="John Doe"
                            :error="form.errors.name"
                            required
                        />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            label="Email Address"
                            placeholder="dealer@example.com"
                            :error="form.errors.email"
                            required
                        />
                        <FormInput
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            label="Mobile Number"
                            placeholder="10-digit number"
                            :error="form.errors.phone"
                            required
                            :maxlength="10"
                            pattern="[0-9]{10}"
                        />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <PasswordInput
                            id="password"
                            v-model="form.password"
                            label="Password"
                            placeholder="Minimum 8 characters"
                            :error="form.errors.password"
                            required
                        />
                        <PasswordInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            label="Confirm Password"
                            placeholder="Repeat password"
                            :error="form.errors.password_confirmation"
                            required
                        />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormInput
                            id="city"
                            v-model="form.city"
                            label="City"
                            placeholder="e.g. Patna"
                            :error="form.errors.city"
                            required
                        />
                        <FormInput
                            id="gst_number"
                            v-model="form.gst_number"
                            label="GST Number (Optional)"
                            placeholder="Enter 15-digit GSTIN"
                            :error="form.errors.gst_number"
                        />
                    </div>
                    
                    <FormInput
                        id="address"
                        v-model="form.address"
                        label="Full Address"
                        placeholder="Dealership complete address"
                        :error="form.errors.address"
                        required
                    />
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" v-model="form.terms" type="checkbox" required class="w-4 h-4 text-[#f97316] bg-gray-100 border-gray-300 rounded focus:ring-[#f97316] focus:ring-2">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">I agree to the <a href="#" class="text-[#f97316] hover:underline">Terms and Conditions</a> and <a href="#" class="text-[#f97316] hover:underline">Privacy Policy</a>.</label>
                        </div>
                    </div>
                    <p v-if="form.errors.terms" class="mt-1.5 text-sm font-medium text-red-600">{{ form.errors.terms }}</p>

                    <div>
                        <button type="submit" :disabled="form.processing || !form.terms" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-[#0f172a] hover:bg-[#0f766e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0f172a] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Submitting...' : 'Register as Dealer' }}
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 text-center text-sm text-gray-600">
                    Already have a dealer account? 
                    <Link href="/dealer/login" class="font-bold text-[#f97316] hover:text-red-700 transition-colors">
                        Login here
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FormInput from '@/Components/Public/FormInput.vue';
import PasswordInput from '@/Components/Public/PasswordInput.vue';
import SuccessState from '@/Components/Public/SuccessState.vue';

const success = ref(false);

const form = useForm({
    business_name: '',
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    city: '',
    address: '',
    gst_number: '',
    terms: false,
});

const submit = () => {
    form.post('/dealer/register', {
        preserveScroll: true,
        onSuccess: () => {
            // Note: If backend redirects on success, this might not hit
            // If it returns Inertia location or same view with success prop, handle accordingly.
            success.value = true;
        },
        onError: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

