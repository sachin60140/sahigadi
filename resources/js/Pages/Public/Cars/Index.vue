<template>
    <Head>
        <title>{{ seoTitle || 'Used Cars for Sale - Buy Pre-owned Cars | SahiGadi' }}</title>
        <meta name="description" :content="seoDescription || 'Browse verified used cars for sale. Find the best deals on pre-owned cars at SahiGadi.'" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />
    </Head>

    <PublicLayout>
        <!-- Hero Section -->
        <div class="relative bg-[#071226] py-16 sm:py-20">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-[url('/images/pattern-bg.png')] opacity-10 bg-repeat bg-center"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#071226] via-[#071226]/90 to-transparent"></div>
            </div>
            
            <div class="relative w-[94%] max-w-[1720px] 2xl:max-w-[1760px] mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex text-sm text-gray-400 mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link href="/" class="hover:text-white transition-colors">Home</Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-gray-300 md:ml-2">Buy Cars</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                            {{ cityName ? `Used Cars in ${cityName}` : (brandName ? `Used ${brandName} Cars` : 'Buy Verified Used Cars in Bihar') }}
                        </h1>
                        <p class="mt-3 max-w-2xl text-lg text-gray-300 font-medium">
                            Search second hand cars by city, brand, budget, fuel type and transmission.
                        </p>
                    </div>

                    <!-- Optional Stats / Trust Badges for large screens -->
                    <div class="hidden lg:flex items-center gap-6 text-sm text-gray-300">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-[#E30613]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="font-bold text-white text-lg leading-none">100%</div>
                                <div>Verified Cars</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-[#E30613]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <div class="font-bold text-white text-lg leading-none">Trusted</div>
                                <div>Local Dealers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#F8FAFC] min-h-screen py-10">
            <div class="w-[94%] max-w-[1720px] 2xl:max-w-[1760px] mx-auto px-4 sm:px-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-8">
                    
                    <!-- Desktop Sidebar Filters -->
                    <aside class="hidden lg:block">
                        <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                            <CarFilters 
                                :filters="filterForm" 
                                :cities="cities" 
                                :brands="brands" 
                                :budget-options="budgetOptions" 
                                :fuel-types="fuelTypes" 
                                :transmissions="transmissions" 
                                @update="handleFilterUpdate"
                                @clear="resetFilters"
                            />
                        </div>
                    </aside>

                    <!-- Main Content -->
                    <main class="min-w-0">
                        <!-- Listing header -->
                        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            
                            <div class="flex items-center justify-between w-full md:w-auto gap-4">
                                <button @click="showMobileFilters = true" class="lg:hidden inline-flex items-center text-sm font-medium text-slate-700 bg-white border border-slate-300 px-4 py-2 rounded-xl hover:bg-slate-50 focus:ring-2 focus:ring-[#071226] transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    Filters
                                </button>
                                
                                <div>
                                    <p class="text-sm text-slate-500 hidden md:block">Showing</p>
                                    <h2 class="text-lg md:text-xl font-bold text-slate-900">{{ cars.total }} verified cars</h2>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 w-full md:w-auto">
                                <SortDropdown 
                                    v-model="filterForm.sort" 
                                    :options="sortOptions" 
                                    @update:modelValue="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Active Filters -->
                        <ActiveFilterChips 
                            :filters="filterForm" 
                            :brands="brands" 
                            @remove="removeFilter" 
                            @clear="resetFilters" 
                        />

                        <!-- Grid -->
                        <div v-if="cars.data.length > 0">
                            <div class="grid gap-6 grid-cols-[repeat(auto-fill,minmax(285px,1fr))]">
                                <CarCard v-for="car in cars.data" :key="car.id" :car="car" />
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-10">
                                <Pagination :links="cars.links" />
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else>
                            <EmptyState @clear="resetFilters" />
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- Mobile Filter Drawer -->
        <MobileFilterDrawer 
            :is-open="showMobileFilters" 
            :filters="filterForm" 
            :cities="cities" 
            :brands="brands" 
            :budget-options="budgetOptions" 
            :fuel-types="fuelTypes" 
            :transmissions="transmissions"
            @close="showMobileFilters = false"
            @update="handleFilterUpdate"
            @clear="resetFilters"
        />
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import CarCard from '@/Components/Public/CarCard.vue';
import CarFilters from '@/Components/Public/CarFilters.vue';
import MobileFilterDrawer from '@/Components/Public/MobileFilterDrawer.vue';
import ActiveFilterChips from '@/Components/Public/ActiveFilterChips.vue';
import SortDropdown from '@/Components/Public/SortDropdown.vue';
import Pagination from '@/Components/Public/Pagination.vue';
import EmptyState from '@/Components/Public/EmptyState.vue';

const props = defineProps<{
    cars: any;
    brands: any[];
    cities: string[];
    fuelTypes: string[];
    transmissions: string[];
    budgetOptions: any[];
    sortOptions: any[];
    seoTitle?: string;
    seoDescription?: string;
    ogImage?: string;
    brandName?: string;
    cityName?: string;
    filters?: any;
}>();

const showMobileFilters = ref(false);

const filterForm = ref({
    keyword: props.filters?.keyword || '',
    city: props.filters?.city || '',
    brand: props.filters?.brand || '',
    max_price: props.filters?.max_price || '',
    fuel_type: props.filters?.fuel_type || '',
    transmission: props.filters?.transmission || '',
    sort: props.filters?.sort || 'relevance',
});

// Sync from props if navigation occurs
watch(() => props.filters, (newFilters) => {
    if (newFilters) {
        filterForm.value = {
            keyword: newFilters.keyword || '',
            city: newFilters.city || '',
            brand: newFilters.brand || '',
            max_price: newFilters.max_price || '',
            fuel_type: newFilters.fuel_type || '',
            transmission: newFilters.transmission || '',
            sort: newFilters.sort || 'relevance',
        };
    }
}, { deep: true });

let timeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    // Debounce to prevent multiple rapid requests
    if (timeout) clearTimeout(timeout);
    
    timeout = setTimeout(() => {
        // Clean up empty params
        const params = Object.fromEntries(
            Object.entries(filterForm.value).filter(([_, v]) => v !== '' && v !== null)
        );

        router.get('/cars', params, {
            preserveState: true,
            preserveScroll: true,
            only: ['cars', 'filters'],
            onSuccess: () => {
                showMobileFilters.value = false;
            }
        });
    }, 300);
};

const handleFilterUpdate = (newFilters: any) => {
    filterForm.value = { ...filterForm.value, ...newFilters };
    applyFilters();
};

const removeFilter = (key: string) => {
    (filterForm.value as any)[key] = '';
    applyFilters();
};

const resetFilters = () => {
    filterForm.value = {
        keyword: '',
        city: '',
        brand: '',
        max_price: '',
        fuel_type: '',
        transmission: '',
        sort: 'relevance',
    };
    applyFilters();
};
</script>
