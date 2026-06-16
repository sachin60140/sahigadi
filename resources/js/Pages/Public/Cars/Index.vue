<template>
    <Head>
        <title>{{ seoTitle || 'Used Cars for Sale - Buy Pre-owned Cars | SahiGadi' }}</title>
        <meta head-key="description" name="description" :content="seoDescription || 'Browse verified used cars for sale. Find the best deals on pre-owned cars at SahiGadi.'" />
        <meta head-key="keywords" name="keywords" content="used cars for sale Bihar, buy second hand cars, verified used cars Patna, used car dealers Bihar" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large" />
        <link head-key="canonical" rel="canonical" :href="canonicalUrl" />
        <meta head-key="og-title" property="og:title" :content="seoTitle || 'Used Cars for Sale - Buy Pre-owned Cars | SahiGadi'" />
        <meta head-key="og-description" property="og:description" :content="seoDescription || 'Browse verified used cars for sale. Find the best deals on pre-owned cars at SahiGadi.'" />
        <meta v-if="ogImage" head-key="og-image" property="og:image" :content="ogImage" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
    </Head>

    <PublicLayout>
        <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f7fbff_0%,#eefbf8_52%,#fff7ed_100%)]">
            <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
                <nav class="mb-5 flex text-sm font-semibold text-slate-500" aria-label="Breadcrumb">
                    <ol class="inline-flex flex-wrap items-center gap-2">
                        <li class="inline-flex items-center">
                            <Link href="/" class="transition hover:text-teal-700">Home</Link>
                        </li>
                        <li class="text-slate-300">/</li>
                        <li class="text-slate-800">Buy Cars</li>
                    </ol>
                </nav>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-end">
                    <div>
                        <div class="mb-4 inline-flex w-fit items-center gap-2 rounded-lg border border-teal-100 bg-white/80 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-teal-700 shadow-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg>
                            SahiGadi search
                        </div>
                        <h1 class="max-w-4xl text-3xl font-semibold leading-tight tracking-normal text-slate-950 sm:text-4xl lg:text-5xl">
                            {{ pageHeading }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                            {{ pageDescription }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <div v-for="stat in heroStats" :key="stat.label" class="rounded-lg border border-white bg-white/75 px-4 py-3 shadow-sm">
                                <p class="text-lg font-semibold text-slate-950">{{ stat.value }}</p>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70">
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Buying workspace</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-950">Shortlist cars with cleaner filters.</h2>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-600">
                            Compare price, city, fuel type and transmission before opening the full listing.
                        </p>
                        <div class="mt-5 grid grid-cols-2 gap-2">
                            <Link href="/cars?sort=newest" class="rounded-lg border border-teal-100 bg-teal-50 px-3 py-3 text-center text-sm font-semibold text-teal-700 transition hover:border-teal-200 hover:bg-white">
                                Latest Cars
                            </Link>
                            <Link href="/sell-your-car" class="rounded-lg border border-orange-100 bg-orange-50 px-3 py-3 text-center text-sm font-semibold text-orange-700 transition hover:border-orange-200 hover:bg-white">
                                Sell Yours
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="min-h-screen bg-[#f7fbff] py-8 md:py-10">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 gap-7 lg:grid-cols-[310px_minmax(0,1fr)]">
                    
                    <!-- Desktop Sidebar Filters -->
                    <aside class="hidden lg:block">
                        <div class="sticky top-24 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
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
                        <div class="mb-5 flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
                            
                            <div class="flex w-full flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between md:w-auto md:gap-4">
                                <button @click="showMobileFilters = true" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:ring-2 focus:ring-teal-700 lg:hidden">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    Filters
                                </button>
                                
                                <div>
                                    <p class="hidden text-sm font-semibold text-slate-500 md:block">Showing</p>
                                    <h2 class="text-lg font-semibold text-slate-950 md:text-xl">{{ resultSummary }}</h2>
                                    <p class="mt-1 hidden text-sm font-medium text-slate-500 md:block">Refine by budget, city, brand, fuel or transmission.</p>
                                </div>
                            </div>
                            
                            <div class="flex w-full min-w-0 items-center gap-3 md:w-auto">
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
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-[repeat(auto-fill,minmax(270px,1fr))]">
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
import { computed, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
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
const page = usePage();

const canonicalUrl = computed(() => String(page.url || '/cars').split('?')[0] || '/cars');

const pageHeading = computed(() => {
    if (props.cityName && props.brandName) {
        return `Used ${props.brandName} Cars in ${props.cityName}`;
    }

    if (props.cityName) {
        return `Used Cars in ${props.cityName}`;
    }

    if (props.brandName) {
        return `Used ${props.brandName} Cars`;
    }

    return 'Buy Verified Used Cars in Bihar';
});

const pageDescription = computed(() => {
    if (props.cityName && props.brandName) {
        return `Compare verified ${props.brandName} cars in ${props.cityName} by budget, fuel type, transmission and seller location.`;
    }

    if (props.cityName) {
        return `Search second hand cars in ${props.cityName} by brand, budget, fuel type and transmission from trusted local sellers.`;
    }

    if (props.brandName) {
        return `Compare used ${props.brandName} cars by price, city, kilometres and seller details across Bihar.`;
    }

    return 'Search second hand cars by city, brand, budget, fuel type and transmission from trusted local sellers across Bihar.';
});

const heroStats = computed(() => [
    { value: new Intl.NumberFormat('en-IN').format(props.cars?.total || 0), label: 'cars listed' },
    { value: `${props.cities?.length || 0}+`, label: 'city filters' },
    { value: `${props.brands?.length || 0}+`, label: 'brand filters' },
]);

const resultSummary = computed(() => {
    const total = Number(props.cars?.total || 0);
    if (!total) return 'No matching cars yet';

    const count = new Intl.NumberFormat('en-IN').format(total);
    return `${count} verified ${total === 1 ? 'car' : 'cars'}`;
});

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
