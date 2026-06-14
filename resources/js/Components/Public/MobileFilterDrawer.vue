<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/70 transition-opacity" @click="$emit('close')"></div>
        
        <div class="relative ml-auto flex h-full w-full max-w-sm flex-col overflow-y-auto bg-white pb-20 shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-4">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Filters</h2>
                    <p class="text-xs font-semibold text-slate-500">Find cars faster</p>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500" @click="$emit('close')">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div>
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
            <div class="fixed bottom-0 right-0 w-full max-w-sm border-t border-slate-200 bg-white p-4">
                <button @click="$emit('close')" class="w-full rounded-lg bg-orange-500 px-4 py-3 text-sm font-black text-white shadow-sm transition hover:bg-orange-600">
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
