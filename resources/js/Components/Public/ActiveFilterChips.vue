<template>
    <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-sm text-gray-500 font-medium">Active Filters:</span>
        
        <span v-if="filters.keyword" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            "{{ filters.keyword }}"
            <button @click="remove('keyword')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>
        
        <span v-if="filters.city" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            City: {{ filters.city }}
            <button @click="remove('city')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>

        <span v-if="filters.brand" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            Brand: {{ brandName }}
            <button @click="remove('brand')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>

        <span v-if="filters.max_price" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            Budget: {{ formatPrice(filters.max_price) }}
            <button @click="remove('max_price')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>

        <span v-if="filters.fuel_type" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            Fuel: {{ filters.fuel_type }}
            <button @click="remove('fuel_type')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>

        <span v-if="filters.transmission" class="inline-flex items-center gap-x-1 rounded-md bg-[#071226] px-2.5 py-1 text-xs font-semibold text-white">
            Trans: {{ filters.transmission }}
            <button @click="remove('transmission')" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-600/20">
                <span class="sr-only">Remove</span>
                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                </svg>
            </button>
        </span>

        <button @click="$emit('clear')" class="text-sm font-medium text-[#E30613] hover:underline ml-2">Clear all</button>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    filters: any;
    brands: any[];
}>();

const emit = defineEmits(['remove', 'clear']);

const hasActiveFilters = computed(() => {
    return props.filters.keyword || 
           props.filters.city || 
           props.filters.brand || 
           props.filters.max_price || 
           props.filters.fuel_type || 
           props.filters.transmission;
});

const brandName = computed(() => {
    if (!props.filters.brand) return '';
    const brand = props.brands.find(b => b.id == props.filters.brand);
    return brand ? brand.name : 'Unknown';
});

const formatPrice = (price: string | number) => {
    const num = Number(price);
    if (num >= 100000) {
        return `Under ₹${(num / 100000).toFixed(1)} Lakh`;
    }
    return `Under ₹${num.toLocaleString('en-IN')}`;
};

const remove = (key: string) => {
    emit('remove', key);
};
</script>
