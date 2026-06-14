<template>
    <Head title="Dealer Login | SahiGadi" />
    
    <AuthLayout title="Dealer Login" subtitle="Manage listings, enquiries and your dealer profile.">
        <AuthCard>
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>
            
            <form @submit.prevent="submit" class="space-y-5">
                <FormInput
                    id="email"
                    v-model="form.email"
                    label="Email Address"
                    type="email"
                    placeholder="dealer@example.com"
                    :error="form.errors.email"
                    required
                    autocomplete="username"
                >
                    <template #prefix>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </template>
                </FormInput>

                <div>
                    <PasswordInput
                        id="password"
                        v-model="form.password"
                        label="Password"
                        placeholder="Enter your password"
                        :error="form.errors.password"
                        required
                        autocomplete="current-password"
                    >
                        <template #prefix>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </template>
                    </PasswordInput>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" v-model="form.remember" class="w-4 h-4 text-[#f97316] bg-gray-100 border-gray-300 rounded focus:ring-[#f97316] focus:ring-2">
                        <label for="remember_me" class="ml-2 text-sm font-medium text-gray-900">Remember me</label>
                    </div>

                    <Link
                        v-if="canResetPassword"
                        href="/dealer/forgot-password"
                        class="text-sm font-semibold text-[#f97316] hover:text-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f97316]"
                    >
                        Forgot your password?
                    </Link>
                </div>

                <div class="mt-6">
                    <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#0f172a] hover:bg-[#0f766e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0f172a] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Logging in...' : 'Login as Dealer' }}
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-center text-gray-600">
                    Not a dealer yet?
                    <Link href="/dealer/register" class="font-bold text-[#f97316] hover:text-red-700 transition-colors">
                        Become a Dealer
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import AuthCard from '@/Components/Public/AuthCard.vue';
import FormInput from '@/Components/Public/FormInput.vue';
import PasswordInput from '@/Components/Public/PasswordInput.vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/dealer/login', {
        preserveScroll: true,
        onError: () => form.reset('password'),
    });
};
</script>

