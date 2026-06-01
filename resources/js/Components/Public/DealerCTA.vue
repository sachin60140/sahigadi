<template>
    <section class="py-16 bg-[#071226] border-b border-white/10 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-[#1E40AF] opacity-20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-[#E30613] opacity-10 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="w-full md:w-2/3 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 tracking-tight">Are you a car dealer?</h2>
                    <p class="text-lg text-gray-400 font-medium">List your cars, manage enquiries and grow your business with SahiGadi.</p>
                </div>
                <div class="w-full md:w-1/3 flex justify-center md:justify-end">
                    <Link href="/dealer/register" class="bg-[#1E40AF] hover:bg-blue-800 text-white font-bold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 group border border-blue-700">
                        Become a Dealer
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
