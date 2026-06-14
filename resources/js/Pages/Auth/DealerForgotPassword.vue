<template>
    <Head title="Forgot Password | SahiGadi" />
    
    <AuthLayout title="Forgot Password" subtitle="Reset your dealer account password.">
        <AuthCard>
            <div class="mb-4 text-sm text-gray-600">
                Forgot your password? No problem. Just let us know your mobile number and we will send you an OTP to reset your password.
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>
            
            <form @submit.prevent="submit" class="space-y-5">
                <FormInput
                    id="phone"
                    v-model="form.phone"
                    label="Mobile Number"
                    type="tel"
                    placeholder="10-digit mobile number"
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

                <div class="mt-6 flex items-center justify-between">
                    <Link
                        href="/dealer/login"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f97316]"
                    >
                        Back to login
                    </Link>

                    <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#0f172a] hover:bg-[#0f766e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0f172a] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Sending...' : 'Send OTP' }}
                    </button>
                </div>
            </form>
        </AuthCard>
    </AuthLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import AuthCard from '@/Components/Public/AuthCard.vue';
import FormInput from '@/Components/Public/FormInput.vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    phone: '',
});

const submit = () => {
    form.post('/dealer/forgot-password/reset', {
        preserveScroll: true,
    });
};
</script>

