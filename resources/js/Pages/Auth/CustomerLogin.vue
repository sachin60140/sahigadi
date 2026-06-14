<template>
    <Head title="Customer Login | SahiGadi" />
    
    <AuthLayout title="Customer Login" subtitle="Access your enquiries, saved cars and profile.">
        <AuthCard>
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>
            
            <form @submit.prevent="submit" class="space-y-5">
                <FormInput
                    id="phone"
                    v-model="form.phone"
                    label="Mobile Number"
                    type="tel"
                    placeholder="Enter your 10-digit mobile number"
                    :error="form.errors.phone"
                    required
                    :maxlength="10"
                    pattern="[0-9]{10}"
                    autocomplete="tel"
                >
                    <template #prefix>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </template>
                </FormInput>

                <div v-if="otpSent">
                    <FormInput
                        id="otp"
                        v-model="form.otp"
                        label="OTP"
                        type="text"
                        placeholder="Enter 6-digit OTP"
                        :error="form.errors.otp"
                        required
                        :maxlength="6"
                        pattern="[0-9]{6}"
                        autocomplete="one-time-code"
                    >
                        <template #prefix>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </template>
                    </FormInput>
                </div>

                <div class="mt-6">
                    <button v-if="!otpSent" type="button" @click="requestOtp" :disabled="form.processing || form.phone.length !== 10" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#f97316] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f97316] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Sending OTP...' : 'Send OTP' }}
                    </button>
                    
                    <button v-else type="submit" :disabled="form.processing || form.otp.length !== 6" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#0f172a] hover:bg-[#0f766e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0f172a] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Verifying...' : 'Login' }}
                    </button>
                </div>
            </form>
        </AuthCard>
    </AuthLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import AuthCard from '@/Components/Public/AuthCard.vue';
import FormInput from '@/Components/Public/FormInput.vue';

const props = defineProps<{
    status?: string;
    actions: {
        sendOtp: string;
        verifyOtp: string;
    };
}>();

const otpSent = ref(false);

const form = useForm({
    phone: '',
    otp: '',
});

const requestOtp = async () => {
    form.clearErrors();
    form.processing = true;
    
    try {
        const response = await axios.post(props.actions.sendOtp, {
            phone: form.phone,
        });
        
        if (response.data.success) {
            otpSent.value = true;
        } else {
            form.setError('phone', response.data.message || 'Failed to send OTP.');
        }
    } catch (error: any) {
        if (error.response?.data?.errors) {
            form.setError(error.response.data.errors);
        } else {
            form.setError('phone', error.response?.data?.message || 'An error occurred.');
        }
    } finally {
        form.processing = false;
    }
};

const submit = async () => {
    form.clearErrors();
    form.processing = true;

    try {
        const response = await axios.post(props.actions.verifyOtp, {
            phone: form.phone,
            otp: form.otp,
        });

        if (response.data.success && response.data.redirect) {
            window.location.assign(response.data.redirect);
            return;
        }

        form.setError('otp', response.data.message || 'Unable to verify OTP.');
        form.reset('otp');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            form.setError(error.response.data.errors);
        } else {
            form.setError('otp', error.response?.data?.message || 'Unable to verify OTP.');
        }
        form.reset('otp');
    } finally {
        form.processing = false;
    }
};
</script>

