<template>
    <Head>
        <title>Login / Register - SahiGadi</title>
        <meta name="description" content="Login or register to manage your used car listings on SahiGadi." />
    </Head>

    <PublicLayout>
        <div class="bg-gray-50 min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-100">
                
                <div class="text-center">
                    <h2 class="mt-2 text-3xl font-extrabold text-gray-900">
                        Welcome Back
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Login or register using your mobile number
                    </p>
                </div>

                <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative text-sm" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>
                <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <!-- Step 1: Request OTP -->
                <form v-if="!otpSent" @submit.prevent="sendOtp" class="mt-8 space-y-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none border-r border-gray-300 pr-2">
                                <span class="text-gray-500 sm:text-sm font-medium">+91</span>
                            </div>
                            <input v-model="phone" type="tel" id="phone" required
                                   pattern="[0-9]{10}" maxlength="10"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-14 sm:text-sm border-gray-300 rounded-lg py-3 font-bold"
                                   placeholder="Enter 10 digit number">
                        </div>
                        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
                    </div>

                    <div>
                        <button type="submit" :disabled="isLoading || phone.length !== 10"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Get Verification Code
                        </button>
                    </div>
                </form>

                <!-- Step 2: Verify OTP -->
                <form v-else @submit.prevent="verifyOtp" class="mt-8 space-y-6 animate-fade-in">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-800 text-center">
                            We've sent a 6-digit verification code to <br>
                            <span class="font-bold text-lg block mt-1">+91 {{ phone }}</span>
                        </p>
                    </div>

                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-1 text-center">Enter Verification Code</label>
                        <input v-model="otp" type="text" id="otp" required
                               pattern="[0-9]{6}" maxlength="6"
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-lg border-gray-300 rounded-lg py-3 text-center tracking-[0.5em] font-bold"
                               placeholder="------">
                        <p v-if="error" class="mt-2 text-sm text-red-600 text-center">{{ error }}</p>
                    </div>

                    <div>
                        <button type="submit" :disabled="isLoading || otp.length !== 6"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Verify & Login
                        </button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" @click="resetForm" class="text-sm text-blue-600 hover:text-blue-500 font-medium transition">
                            Change Mobile Number
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import axios from 'axios';

const phone = ref('');
const otp = ref('');
const otpSent = ref(false);
const isLoading = ref(false);
const error = ref('');

const sendOtp = async () => {
    error.value = '';
    isLoading.value = true;
    
    try {
        const response = await axios.post('/customer/send-otp', { phone: phone.value });
        if (response.data.success) {
            otpSent.value = true;
        } else {
            error.value = response.data.message || 'Failed to send OTP.';
        }
    } catch (e: any) {
        error.value = e.response?.data?.message || 'Server error. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

const verifyOtp = async () => {
    error.value = '';
    isLoading.value = true;
    
    try {
        const response = await axios.post('/customer/verify-otp', { 
            phone: phone.value,
            otp: otp.value
        });
        
        if (response.data.success) {
            // Success! The backend returns a redirect URL
            // We use Inertia router to perform the hard or soft visit to the dashboard
            window.location.href = response.data.redirect || '/customer/dashboard';
        } else {
            error.value = response.data.message || 'Invalid OTP.';
            isLoading.value = false;
        }
    } catch (e: any) {
        error.value = e.response?.data?.message || 'Server error. Please try again.';
        isLoading.value = false;
    }
};

const resetForm = () => {
    otpSent.value = false;
    otp.value = '';
    error.value = '';
};
</script>
