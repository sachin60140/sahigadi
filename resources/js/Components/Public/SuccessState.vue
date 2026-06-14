<template>
    <div class="text-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6 shadow-sm">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h3 class="text-2xl font-extrabold text-[#0f172a] mb-3">{{ title }}</h3>
        <p class="text-gray-500 max-w-md mx-auto">{{ message }}</p>
        
        <div class="mt-8">
            <slot>
                <Link href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-[#f97316] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f97316] transition-colors">
                    Back to Homepage
                </Link>
            </slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    message: string;
}>();
</script>

