<template>
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-8 md:p-12 shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="w-full md:w-2/3 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Sell your car faster with SahiGadi</h2>
                    <p class="text-lg text-gray-600">Submit your car details and connect with verified buyers and dealers.</p>
                </div>
                <div class="w-full md:w-1/3 flex justify-center md:justify-end">
                    <Link href="/sell-your-car" class="bg-[#f97316] hover:bg-red-700 text-white font-bold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 group">
                        Sell Your Car
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

