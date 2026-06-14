<template>
    <section v-if="brands && brands.length > 0" class="border-y border-slate-100 bg-slate-50 py-14">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="mb-2 text-sm font-black uppercase tracking-[0.16em] text-teal-700">Top brands</p>
                    <h2 class="text-2xl font-black tracking-normal text-slate-950">Browse by Brand</h2>
                    <p class="mt-2 text-sm font-medium text-slate-500">Explore used cars from popular manufacturers.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                <Link v-for="brand in brands.slice(0, 12)" :key="brand.id" :href="`/used-${brand.slug}-cars`" 
                      class="group flex min-h-[118px] flex-col items-center justify-center rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-md">
                    <img v-if="brand.logo" :src="`/storage/${brand.logo}`" :alt="`${brand.name} used cars`" class="mb-3 h-10 w-10 object-contain transition group-hover:scale-105 md:h-12 md:w-12" />
                    <span v-else class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-700 md:h-12 md:w-12">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-9 0a3 3 0 11-6 0 3 3 0 016 0zm17 0a3 3 0 11-6 0 3 3 0 016 0zM5 17H3l2-6h14l2 6h-2" />
                        </svg>
                    </span>
                    <span class="text-sm font-black text-slate-800 transition group-hover:text-teal-700">{{ brand.name }}</span>
                    <span class="mt-1 text-xs font-bold text-slate-400">{{ brand.cars_count || 0 }} Cars</span>
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    brands: any[];
}>();
</script>
