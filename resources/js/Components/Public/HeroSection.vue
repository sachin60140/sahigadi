<template>
    <div class="relative bg-[#071226] min-h-[620px] xl:min-h-[700px] flex items-center overflow-hidden">
        <!-- Background Image & Gradients -->
        <div class="absolute inset-0">
            <img src="/images/hero-bg.png" alt="SahiGadi Premium Cars" class="w-full h-full object-cover opacity-80" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#071226] via-[#071226]/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-[#071226]/40 via-transparent to-[#071226]"></div>
            
            <!-- Dotted Pattern Overlay -->
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 z-10 flex flex-col md:flex-row items-center gap-12">
            
            <!-- Left Content -->
            <div class="w-full md:w-[55%] text-white">
                <!-- Trust Badge -->
                <div class="inline-flex items-center gap-2 mb-6 opacity-90">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-xs sm:text-sm font-bold tracking-wider text-blue-300 uppercase">
                        Bihar's Most Trusted Used Car Marketplace
                    </span>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-black tracking-tight mb-6 leading-[1.1]">
                    Verified Used Cars <br />
                    in <span class="text-[#E30613]">Bihar</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-xl font-medium leading-relaxed">
                    Buy, sell and verify pre-owned cars with complete confidence. Direct from owners and verified dealers.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <Link href="/cars" class="bg-[#E30613] hover:bg-red-700 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 group">
                        Browse Cars
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </Link>
                    <Link href="/sell-your-car" class="bg-transparent border-2 border-white/30 hover:border-white hover:bg-white/5 text-white font-bold px-8 py-4 rounded-xl transition-all flex items-center justify-center gap-2 backdrop-blur-sm">
                        Sell Your Car
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </Link>
                </div>
            </div>

            <!-- Right Content (Search Card) -->
            <div class="w-full md:w-[45%]">
                <HeroSearchCard 
                    :cities="cities" 
                    :brands="brands" 
                    :budget-options="budgetOptions" 
                    :fuel-types="fuelTypes" 
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import HeroSearchCard from './HeroSearchCard.vue';

defineProps<{
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
}>();
</script>
