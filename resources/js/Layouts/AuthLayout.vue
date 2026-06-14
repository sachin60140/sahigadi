<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 pointer-events-none opacity-30 " style="background-size: 400px;"></div>
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <Link href="/" class="flex justify-center mb-6">
                <span class="text-3xl font-black text-[#0f172a] tracking-tight">SAHI<span class="text-[#f97316]">GADI</span></span>
            </Link>
            
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a]">{{ title }}</h2>
                <p class="mt-2 text-sm text-gray-600 font-medium">
                    {{ subtitle }}
                </p>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <slot />
        </div>
        
        <div class="mt-8 text-center text-xs text-gray-500 relative z-10">
            &copy; {{ new Date().getFullYear() }} SahiGadi.com. All rights reserved.
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    subtitle: string;
}>();
</script>

