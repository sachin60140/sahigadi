<template>
    <section class="relative flex min-h-[620px] items-center overflow-hidden bg-slate-950 xl:min-h-[680px]">
        <div class="absolute inset-0">
            <img src="/images/hero-bg.png" alt="Verified used cars available on SahiGadi" class="h-full w-full object-cover opacity-85" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/82 to-slate-950/20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 mx-auto flex w-full max-w-[1440px] flex-col items-center gap-10 px-4 py-14 sm:px-6 md:flex-row md:py-20 lg:px-8">
            <div class="w-full text-white md:w-[55%]">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 backdrop-blur-sm">
                    <svg class="h-5 w-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-xs font-black uppercase tracking-[0.16em] text-teal-100 sm:text-sm">
                        Bihar's Most Trusted Used Car Marketplace
                    </span>
                </div>

                <h1 class="mb-6 max-w-3xl text-4xl font-black leading-[1.05] tracking-normal sm:text-5xl lg:text-7xl">
                    Verified Used Cars <br />
                    in <span class="text-orange-400">Bihar</span>
                </h1>

                <p class="mb-8 max-w-xl text-lg font-medium leading-8 text-slate-200 md:text-xl">
                    Buy, sell, and verify pre-owned cars with transparent pricing, trusted local dealers, owner listings, and secure contact unlock.
                </p>

                <div class="mb-8 flex flex-col gap-3 sm:flex-row">
                    <Link href="/cars" class="group inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-7 py-4 text-base font-black text-white shadow-lg shadow-orange-950/20 transition hover:-translate-y-0.5 hover:bg-orange-600">
                        Browse Cars
                        <svg class="h-5 w-5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                    <Link href="/sell-your-car" class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 bg-white/10 px-7 py-4 text-base font-black text-white backdrop-blur-sm transition hover:bg-white/15">
                        Sell Your Car
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </Link>
                </div>

                <div class="grid max-w-xl grid-cols-3 gap-3 text-sm">
                    <div class="rounded-lg border border-white/10 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-lg font-black text-white">100%</div>
                        <div class="text-slate-300">Verified</div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-lg font-black text-white">OTP</div>
                        <div class="text-slate-300">Safe Contact</div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-lg font-black text-white">Bihar</div>
                        <div class="text-slate-300">Local Deals</div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-[45%]">
                <HeroSearchCard 
                    :cities="cities" 
                    :brands="brands" 
                    :budget-options="budgetOptions" 
                    :fuel-types="fuelTypes" 
                />
            </div>
        </div>
    </section>
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
