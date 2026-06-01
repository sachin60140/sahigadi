<template>
    <Link :href="`/car/${car.slug}`" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl flex flex-col h-full">
        <!-- Image Container -->
        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
            <img :src="imageUrl" :alt="title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" @error="handleImageError" />
            
            <!-- Wishlist/Heart Button -->
            <button @click.prevent class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>
            
            <!-- Verified Badge -->
            <div v-if="isVerified" class="absolute top-3 left-3 flex gap-2 items-start">
                <span class="bg-[#16A34A] text-white text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider flex items-center shadow-sm">
                    Verified
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5 flex flex-col flex-grow">
            <!-- Title & Variant -->
            <h3 class="text-slate-900 font-extrabold text-lg leading-tight line-clamp-1 group-hover:text-[#1E40AF] transition-colors">
                {{ title }}
            </h3>
            <p class="text-sm text-slate-500 mt-1 line-clamp-1 font-medium">{{ car.variant || car.model || 'Standard Variant' }}</p>
            
            <!-- Location -->
            <div class="flex items-center gap-1.5 mt-2.5 text-xs text-slate-400 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>{{ car.city || 'Bihar' }}, Bihar</span>
            </div>

            <!-- Specs Inline (Year | Fuel | Transmission) -->
            <div class="flex items-center gap-3 mt-4 text-[13px] text-slate-600 font-medium">
                <div class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ car.year || 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    <span>{{ car.fuel_type || 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ car.transmission || 'Manual' }}</span>
                </div>
            </div>

            <div class="flex-grow"></div>

            <!-- Price & KM -->
            <div class="mt-6 flex flex-col gap-2">
                <div class="text-[22px] font-black text-[#E30613]">
                    ₹ {{ formattedPrice }}
                </div>
                <div class="self-start bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-full tracking-wide">
                    {{ car.km_driven?.toLocaleString('en-IN') || '0' }} km
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="w-full text-center text-sm font-semibold text-[#071226] group-hover:text-[#E30613] transition-colors">
                    View Details
                </div>
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    car: any;
}>();

const title = computed(() => {
    const brandName = props.car.brand?.name || '';
    const model = props.car.model || '';
    return props.car.title || `${brandName} ${model}`.trim();
});

const imageUrl = computed(() => {
    return props.car.image_url || '/images/car-placeholder.webp';
});

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.src = '/images/car-placeholder.webp';
};

const isVerified = computed(() => {
    return props.car.is_verified || false;
});

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0
    }).format(props.car.price || 0);
});
</script>
