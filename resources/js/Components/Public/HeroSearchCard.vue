<template>
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-6 md:p-8 relative z-10 w-full max-w-md mx-auto xl:mr-0 xl:ml-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">Find your perfect car</h2>
        <p class="text-sm text-gray-500 mb-6">Search from thousands of verified cars across Bihar</p>
        
        <form @submit.prevent="submitSearch" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">City</label>
                    <div class="relative">
                        <select v-model="form.city" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg shadow-sm focus:ring-[#1E40AF] focus:border-[#1E40AF] appearance-none bg-gray-50 hover:bg-white transition-colors cursor-pointer text-gray-700">
                            <option value="">All Cities</option>
                            <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Brand</label>
                    <div class="relative">
                        <select v-model="form.brand" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg shadow-sm focus:ring-[#1E40AF] focus:border-[#1E40AF] appearance-none bg-gray-50 hover:bg-white transition-colors cursor-pointer text-gray-700">
                            <option value="">All Brands</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Budget</label>
                    <div class="relative">
                        <select v-model="form.max_price" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg shadow-sm focus:ring-[#1E40AF] focus:border-[#1E40AF] appearance-none bg-gray-50 hover:bg-white transition-colors cursor-pointer text-gray-700">
                            <option value="">Any Budget</option>
                            <option v-for="option in budgetOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Fuel Type</label>
                    <div class="relative">
                        <select v-model="form.fuel_type" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg shadow-sm focus:ring-[#1E40AF] focus:border-[#1E40AF] appearance-none bg-gray-50 hover:bg-white transition-colors cursor-pointer text-gray-700">
                            <option value="">Any Fuel</option>
                            <option v-for="fuel in fuelTypes" :key="fuel" :value="fuel">{{ fuel }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-[#0B1F3A] hover:bg-[#071226] text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition-all hover:shadow-lg flex justify-center items-center gap-2 mt-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Search Cars
            </button>
            
            <div class="flex items-center justify-center pt-3 border-t border-gray-100">
                <svg class="w-4 h-4 text-green-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="text-xs text-gray-500 font-medium">Every car is verified for a safe and trusted experience</span>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
}>();

const form = useForm({
    city: '',
    brand: '',
    max_price: '',
    fuel_type: '',
});

const submitSearch = () => {
    form.get('/cars', {
        preserveState: true,
    });
};
</script>
