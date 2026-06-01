<template>
    <section v-if="brands && brands.length > 0" class="py-16 bg-[#F8FAFC] border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-[#071226]">Browse by Brand</h2>
                    <p class="mt-2 text-gray-500 font-medium">Explore cars from top manufacturers</p>
                </div>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
                <Link v-for="brand in brands.slice(0, 12)" :key="brand.id" :href="`/used-${brand.slug}-cars`" 
                      class="bg-white border border-gray-100 rounded-xl p-4 flex flex-col items-center justify-center hover:border-[#1E40AF] hover:shadow-md transition-all group">
                    <img v-if="brand.logo" :src="`/storage/${brand.logo}`" :alt="brand.name" class="h-10 w-10 md:h-12 md:w-12 object-contain mb-3 group-hover:scale-110 transition-transform" />
                    <span class="font-bold text-gray-800 text-center text-sm group-hover:text-[#1E40AF] transition-colors">{{ brand.name }}</span>
                    <span class="text-[10px] sm:text-xs text-gray-400 mt-1 font-semibold">{{ brand.cars_count || 0 }} Cars</span>
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
