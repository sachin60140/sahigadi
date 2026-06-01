<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/80 transition-opacity" @click="$emit('close')"></div>
        
        <div class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-12 shadow-xl">
            <div class="flex items-center justify-between px-4">
                <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                <button type="button" class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md bg-white p-2 text-gray-400" @click="$emit('close')">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-4 border-t border-gray-200">
                <CarFilters 
                    :filters="filters" 
                    :cities="cities" 
                    :brands="brands" 
                    :budget-options="budgetOptions" 
                    :fuel-types="fuelTypes" 
                    :transmissions="transmissions" 
                    @update="handleUpdate"
                    @clear="handleClear"
                />
            </div>

            <!-- Sticky apply button at bottom -->
            <div class="fixed bottom-0 w-full max-w-xs border-t border-gray-200 bg-white p-4">
                <button @click="$emit('close')" class="w-full rounded-md bg-[#E30613] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import CarFilters from './CarFilters.vue';

defineProps<{
    isOpen: boolean;
    filters: any;
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
    transmissions: string[];
}>();

const emit = defineEmits(['close', 'update', 'clear']);

const handleUpdate = (newFilters: any) => {
    emit('update', newFilters);
};

const handleClear = () => {
    emit('clear');
};
</script>
