<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm sm:p-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Header -->
            <div class="bg-[#071226] p-5 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg">View Contact Details</h3>
                <button @click="closeModal" class="text-gray-300 hover:text-white transition-colors p-1" :disabled="loading">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 md:p-8">
                <!-- Step 1: Request OTP -->
                <div v-if="step === 1">
                    <p class="text-gray-600 mb-6 text-sm">Please verify your mobile number to view the seller's contact details. This keeps our platform secure for everyone.</p>
                    
                    <form @submit.prevent="sendOtp" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input 
                                type="text" 
                                v-model="form.viewer_name" 
                                required 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#E30613] focus:border-[#E30613] transition-all bg-gray-50 focus:bg-white"
                                placeholder="Enter your full name"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-500 font-medium">+91</span>
                                <input 
                                    type="tel" 
                                    v-model="form.viewer_mobile" 
                                    required 
                                    maxlength="10" 
                                    pattern="[0-9]{10}"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#E30613] focus:border-[#E30613] transition-all bg-gray-50 focus:bg-white"
                                    placeholder="Enter 10 digit number"
                                >
                            </div>
                        </div>
                        
                        <div v-if="errorMsg" class="text-red-500 text-sm font-medium p-3 bg-red-50 rounded-lg border border-red-100">{{ errorMsg }}</div>
                        
                        <button 
                            type="submit" 
                            :disabled="loading || form.viewer_mobile.length !== 10" 
                            class="w-full bg-[#E30613] hover:bg-red-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-md disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center mt-2"
                        >
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ loading ? 'Sending OTP...' : 'Send OTP' }}
                        </button>
                    </form>
                </div>

                <!-- Step 2: Verify OTP -->
                <div v-else-if="step === 2">
                    <div class="text-center mb-6">
                        <div class="mx-auto w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Enter Verification Code</h4>
                        <p class="text-sm text-gray-500 mt-1">We've sent a 6-digit OTP to +91 {{ form.viewer_mobile }}</p>
                        <button @click="step = 1; errorMsg = ''" class="text-xs text-blue-600 font-semibold mt-2 hover:underline">Change Number</button>
                    </div>

                    <form @submit.prevent="verifyOtp" class="space-y-4">
                        <div>
                            <input 
                                type="text" 
                                v-model="otp" 
                                required 
                                maxlength="6" 
                                pattern="[0-9]{6}"
                                class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#E30613] focus:border-[#E30613] transition-all bg-gray-50 focus:bg-white"
                                placeholder="••••••"
                            >
                        </div>
                        
                        <div v-if="errorMsg" class="text-red-500 text-sm font-medium p-3 bg-red-50 rounded-lg border border-red-100 text-center">{{ errorMsg }}</div>
                        
                        <button 
                            type="submit" 
                            :disabled="loading || otp.length !== 6" 
                            class="w-full bg-[#E30613] hover:bg-red-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-md disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center mt-2"
                        >
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ loading ? 'Verifying...' : 'Verify OTP' }}
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <p v-if="resendTimer > 0" class="text-gray-500">
                            Resend OTP in <span class="font-bold text-gray-900">{{ resendTimer }}s</span>
                        </p>
                        <button v-else @click="sendOtp" :disabled="loading" class="text-[#071226] font-bold hover:underline disabled:opacity-50">
                            Resend OTP
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Trust Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Secure 256-bit Encrypted Process
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps<{
    isOpen: boolean;
    carId?: number;
    customerCarListingId?: number;
}>();

const emit = defineEmits(['close', 'verified']);

const step = ref(1);
const loading = ref(false);
const errorMsg = ref('');
const otp = ref('');
const logId = ref(null);
const resendTimer = ref(0);
let timerInterval: any = null;

const form = reactive({
    viewer_name: '',
    viewer_mobile: '',
});

watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        step.value = 1;
        errorMsg.value = '';
        otp.value = '';
        if (!form.viewer_name) form.viewer_name = '';
        if (!form.viewer_mobile) form.viewer_mobile = '';
    } else {
        clearInterval(timerInterval);
    }
});

onUnmounted(() => {
    clearInterval(timerInterval);
});

const startTimer = () => {
    resendTimer.value = 60;
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (resendTimer.value > 0) {
            resendTimer.value--;
        } else {
            clearInterval(timerInterval);
        }
    }, 1000);
};

const closeModal = () => {
    if (!loading.value) {
        emit('close');
    }
};

const sendOtp = async () => {
    errorMsg.value = '';
    loading.value = true;
    
    try {
        const payload = {
            viewer_name: form.viewer_name,
            viewer_mobile: form.viewer_mobile,
            car_id: props.carId,
            customer_car_listing_id: props.customerCarListingId,
            source_page: window.location.href,
        };
        
        const response = await axios.post('/otp/send-contact-unlock', payload);
        
        if (response.data.success) {
            logId.value = response.data.log_id;
            step.value = 2;
            otp.value = '';
            startTimer();
        } else {
            errorMsg.value = response.data.message || 'Failed to send OTP. Please try again.';
        }
    } catch (error: any) {
        errorMsg.value = error.response?.data?.message || 'An error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const verifyOtp = async () => {
    errorMsg.value = '';
    loading.value = true;
    
    try {
        const response = await axios.post('/otp/verify-contact-unlock', {
            log_id: logId.value,
            otp: otp.value
        });
        
        if (response.data.success) {
            emit('verified', response.data.contact_number);
            emit('close');
        } else {
            errorMsg.value = response.data.message || 'Invalid OTP.';
            otp.value = '';
        }
    } catch (error: any) {
        errorMsg.value = error.response?.data?.message || 'An error occurred during verification.';
        otp.value = '';
    } finally {
        loading.value = false;
    }
};
</script>
