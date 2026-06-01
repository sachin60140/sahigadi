<template>
    <section v-if="cars && cars.length > 0" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10 gap-4">
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#071226] tracking-tight">Recently Added Cars</h2>
                    <p class="mt-2 text-gray-500 font-medium">Fresh verified used cars listed on SahiGadi</p>
                </div>
                <Link href="/cars" class="inline-flex items-center text-[#071226] font-bold bg-white border border-gray-300 hover:border-gray-400 hover:bg-gray-50 px-6 py-2.5 rounded-full transition-all shadow-sm">
                    View All Cars
                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </Link>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                <CarCard v-for="car in cars" :key="car.id" :car="car" />
            </div>
            
            <div class="mt-10 text-center sm:hidden">
                <Link href="/cars" class="inline-flex justify-center items-center w-full text-white font-bold bg-[#071226] hover:bg-[#0B1F3A] px-6 py-3.5 rounded-xl transition-all shadow-md">
                    View All Cars
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import CarCard from './CarCard.vue';

defineProps<{
    cars: any[];
}>();
</script>
