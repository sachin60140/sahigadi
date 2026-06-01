<template>
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#071226] mb-8">Popular Cities in Bihar</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <Link v-for="city in popularCities" :key="city" :href="`/used-cars-in-${city.toLowerCase()}`" 
                      class="bg-gray-50 border border-gray-100 rounded-xl p-4 flex items-center justify-center gap-2 hover:bg-[#071226] hover:text-white transition-all hover:shadow-md group">
                    <svg class="w-5 h-5 text-[#E30613] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-bold text-gray-800 group-hover:text-white transition-colors">{{ city }}</span>
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

// As requested, hardcode the popular ones if they are always needed, or filter from props.
const popularCities = ['Muzaffarpur', 'Patna', 'Motihari', 'Darbhanga', 'Gaya', 'Bhagalpur'];
</script>
