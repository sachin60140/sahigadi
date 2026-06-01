<template>
    <div class="bg-white p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">Filters</h2>
            <button @click="$emit('clear')" class="text-sm font-medium text-[#E30613] hover:text-red-700">Clear All</button>
        </div>

        <!-- Keyword Search -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Search</label>
            <input type="text" v-model="localFilters.keyword" @change="updateFilters" placeholder="Search by model or variant..." class="w-full rounded-xl border-slate-300 focus:border-[#071226] focus:ring-[#071226] text-sm py-2.5" />
        </div>

        <!-- City -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">City</label>
            <select v-model="localFilters.city" @change="updateFilters" class="w-full rounded-xl border-slate-300 focus:border-[#071226] focus:ring-[#071226] text-sm py-2.5">
                <option value="">All Cities</option>
                <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
            </select>
        </div>

        <!-- Brand -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Brand</label>
            <select v-model="localFilters.brand" @change="updateFilters" class="w-full rounded-xl border-slate-300 focus:border-[#071226] focus:ring-[#071226] text-sm py-2.5">
                <option value="">All Brands</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
            </select>
        </div>

        <!-- Budget -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Max Budget</label>
            <select v-model="localFilters.max_price" @change="updateFilters" class="w-full rounded-xl border-slate-300 focus:border-[#071226] focus:ring-[#071226] text-sm py-2.5">
                <option value="">Any Budget</option>
                <option v-for="opt in budgetOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
        </div>

        <!-- Fuel Type -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Fuel Type</label>
            <div class="space-y-2.5 mt-2">
                <label v-for="fuel in fuelTypes" :key="fuel" class="flex items-center space-x-3">
                    <input type="radio" v-model="localFilters.fuel_type" :value="fuel" @change="updateFilters" class="w-4 h-4 text-[#071226] border-slate-300 focus:ring-[#071226]">
                    <span class="text-sm text-slate-600 font-medium">{{ fuel }}</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="radio" v-model="localFilters.fuel_type" value="" @change="updateFilters" class="w-4 h-4 text-[#071226] border-slate-300 focus:ring-[#071226]">
                    <span class="text-sm text-slate-600 font-medium">Any</span>
                </label>
            </div>
        </div>

        <!-- Transmission -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Transmission</label>
            <div class="space-y-2.5 mt-2">
                <label v-for="trans in transmissions" :key="trans" class="flex items-center space-x-3">
                    <input type="radio" v-model="localFilters.transmission" :value="trans" @change="updateFilters" class="w-4 h-4 text-[#071226] border-slate-300 focus:ring-[#071226]">
                    <span class="text-sm text-slate-600 font-medium">{{ trans }}</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="radio" v-model="localFilters.transmission" value="" @change="updateFilters" class="w-4 h-4 text-[#071226] border-slate-300 focus:ring-[#071226]">
                    <span class="text-sm text-slate-600 font-medium">Any</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <button @click="updateFilters" class="w-full h-12 rounded-xl bg-[#071226] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#0B1F3A] transition-colors flex items-center justify-center">
                Apply Filters
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    filters: any;
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
    transmissions: string[];
}>();

const emit = defineEmits(['update', 'clear']);

const localFilters = ref({
    keyword: props.filters.keyword || '',
    city: props.filters.city || '',
    brand: props.filters.brand || '',
    max_price: props.filters.max_price || '',
    fuel_type: props.filters.fuel_type || '',
    transmission: props.filters.transmission || '',
    sort: props.filters.sort || ''
});

watch(() => props.filters, (newFilters) => {
    localFilters.value = {
        keyword: newFilters.keyword || '',
        city: newFilters.city || '',
        brand: newFilters.brand || '',
        max_price: newFilters.max_price || '',
        fuel_type: newFilters.fuel_type || '',
        transmission: newFilters.transmission || '',
        sort: newFilters.sort || ''
    };
}, { deep: true });

const updateFilters = () => {
    emit('update', localFilters.value);
};
</script>
