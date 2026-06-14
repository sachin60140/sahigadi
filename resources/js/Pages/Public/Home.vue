<template>
    <Head>
        <title>SahiGadi - Verified Used Cars in Bihar | Buy & Sell Second Hand Cars</title>
        <meta head-key="description" name="description" content="Buy, sell and compare pre-owned cars in Bihar. SahiGadi helps buyers explore verified used cars and trusted sellers across Patna, Muzaffarpur, Darbhanga and more." />
        <meta head-key="keywords" name="keywords" content="used cars in Bihar, second hand cars Muzaffarpur, buy used cars Patna, sell used car Bihar, verified used cars" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
        <link head-key="canonical" rel="canonical" href="/" />
        <meta head-key="og-title" property="og:title" content="SahiGadi - Verified Used Cars in Bihar" />
        <meta head-key="og-description" property="og:description" content="A cleaner way to find verified used cars and trusted sellers across Bihar." />
        <meta head-key="og-image" property="og:image" content="/images/og-image.png" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />

        <component is="script" head-key="schema-home" type="application/ld+json" v-html="schemaJson"></component>
    </Head>

    <PublicLayout>
        <main class="bg-[#f7fbff] text-slate-950">
            <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f7fbff_0%,#eefbf8_48%,#fff7ed_100%)]">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 sm:py-14 lg:grid-cols-[minmax(0,1.08fr)_minmax(360px,0.92fr)] lg:px-8 lg:py-16">
                    <div class="flex flex-col justify-center">
                        <div class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg border border-teal-100 bg-white/80 px-3 py-2 text-xs font-black uppercase tracking-wide text-teal-700 shadow-sm">
                            <span class="grid h-5 w-5 place-items-center rounded-md bg-teal-600 text-white">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Verified cars, simpler decisions
                        </div>

                        <h1 class="max-w-4xl text-3xl font-black leading-tight tracking-normal text-slate-950 sm:text-5xl lg:text-6xl">
                            Find a used car that feels right before you call.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                            Compare inspected listings, local sellers and city-wise inventory in one calm, transparent buying journey.
                        </p>

                        <form class="mt-7 rounded-lg border border-slate-200 bg-white p-3 shadow-xl shadow-slate-200/70 sm:p-4" @submit.prevent="submitSearch">
                            <div class="grid gap-3 lg:grid-cols-[minmax(0,1.4fr)_minmax(160px,0.8fr)_minmax(160px,0.8fr)_auto]">
                                <label class="block">
                                    <span class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-500">Search model</span>
                                    <input
                                        v-model="searchForm.keyword"
                                        type="search"
                                        class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100"
                                        placeholder="Swift, Creta, Baleno..."
                                    />
                                </label>

                                <label class="block">
                                    <span class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-500">City</span>
                                    <select
                                        v-model="searchForm.city"
                                        class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100"
                                    >
                                        <option value="">All Cities</option>
                                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                                    </select>
                                </label>

                                <label class="block">
                                    <span class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-500">Budget</span>
                                    <select
                                        v-model="searchForm.max_price"
                                        class="h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100"
                                    >
                                        <option value="">Any Budget</option>
                                        <option v-for="option in budgetOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                                    </select>
                                </label>

                                <button
                                    type="submit"
                                    class="mt-1 inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-slate-950 px-5 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 hover:bg-teal-700 lg:mt-6"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                    </svg>
                                    Search
                                </button>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <button
                                    v-for="fuel in fuelTypes"
                                    :key="fuel"
                                    type="button"
                                    class="rounded-md border px-3 py-2 text-xs font-black transition"
                                    :class="searchForm.fuel_type === normalizeOption(fuel)
                                        ? 'border-teal-600 bg-teal-50 text-teal-700'
                                        : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                                    @click="toggleFuel(fuel)"
                                >
                                    {{ fuel }}
                                </button>
                            </div>
                        </form>

                        <div class="mt-7 grid max-w-3xl gap-3 sm:grid-cols-3">
                            <div v-for="stat in stats" :key="stat.label" class="rounded-lg border border-white bg-white/75 p-3 shadow-sm sm:p-4">
                                <p class="text-xl font-black text-slate-950 sm:text-2xl">{{ stat.value }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid content-center gap-4">
                        <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-2xl shadow-slate-200/80">
                            <div class="relative overflow-hidden rounded-lg bg-slate-100">
                                <img
                                    :src="imageFor(heroCar)"
                                    :alt="heroCar ? `${carTitle(heroCar)} used car` : 'SahiGadi verified used car'"
                                    class="aspect-[4/3] w-full object-cover"
                                    loading="eager"
                                />
                                <div class="absolute inset-x-3 bottom-3 rounded-lg border border-white/60 bg-white/90 p-3 shadow-lg backdrop-blur">
                                <div class="flex flex-col gap-2 min-[420px]:flex-row min-[420px]:items-end min-[420px]:justify-between min-[420px]:gap-3">
                                        <div class="min-w-0">
                                            <p class="text-xs font-black uppercase tracking-wide text-teal-700">Fresh pick</p>
                                            <h2 class="mt-1 truncate text-lg font-black text-slate-950">{{ heroCar ? carTitle(heroCar) : 'Verified used cars' }}</h2>
                                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ heroCar?.city || 'Across Bihar' }}</p>
                                        </div>
                                        <p class="shrink-0 text-lg font-black text-orange-600">{{ heroCar ? formatPrice(heroCar.price) : 'Browse' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <Link
                                href="/sell-your-car"
                                class="rounded-lg border border-orange-100 bg-orange-50 p-4 transition hover:-translate-y-0.5 hover:border-orange-200 hover:bg-white"
                            >
                                <p class="text-sm font-black text-orange-700">Sell smarter</p>
                                <p class="mt-1 text-xs font-semibold leading-5 text-slate-600">Create a clean listing for local buyers.</p>
                            </Link>
                            <Link
                                href="/verified-dealers"
                                class="rounded-lg border border-teal-100 bg-teal-50 p-4 transition hover:-translate-y-0.5 hover:border-teal-200 hover:bg-white"
                            >
                                <p class="text-sm font-black text-teal-700">Find dealers</p>
                                <p class="mt-1 text-xs font-semibold leading-5 text-slate-600">Browse trusted local inventory.</p>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <component
                        v-for="item in journeyCards"
                        :key="item.title"
                        :is="fullPageRoutes.has(item.href) ? 'a' : Link"
                        :href="item.href"
                        class="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg"
                    >
                        <div class="mb-4 grid h-10 w-10 place-items-center rounded-lg" :class="item.iconClass">
                            <component :is="item.icon" class="h-5 w-5" />
                        </div>
                        <h2 class="text-lg font-black text-slate-950">{{ item.title }}</h2>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ item.text }}</p>
                        <span class="mt-4 inline-flex text-sm font-black text-teal-700 group-hover:text-orange-600">{{ item.action }}</span>
                    </component>
                </div>
            </section>

            <section class="bg-white">
                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-orange-600">Curated inventory</p>
                            <h2 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Cars worth shortlisting today</h2>
                            <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-slate-600">
                                Featured and recently added cars from local sellers, arranged for quick comparison.
                            </p>
                        </div>
                        <Link href="/cars" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-black text-slate-900 transition hover:border-teal-600 hover:text-teal-700 sm:w-fit">
                            View all cars
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>

                    <div v-if="displayCars.length" class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        <Link
                            v-for="car in displayCars"
                            :key="car.slug || car.id"
                            :href="`/car/${car.slug}`"
                            class="group overflow-hidden rounded-lg border border-slate-200 bg-[#fbfdff] shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-xl"
                        >
                            <div class="relative aspect-[16/11] overflow-hidden bg-slate-100">
                                <img
                                    :src="imageFor(car)"
                                    :alt="`${carTitle(car)} used car in ${car.city || 'Bihar'}`"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                    loading="lazy"
                                />
                                <div class="absolute left-3 top-3 flex gap-2">
                                    <span v-if="car.is_verified" class="rounded-md bg-white px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-teal-700 shadow-sm">Verified</span>
                                    <span v-if="car.is_featured" class="rounded-md bg-orange-500 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-white shadow-sm">Featured</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="line-clamp-2 text-base font-black leading-snug text-slate-950 group-hover:text-teal-700">{{ carTitle(car) }}</h3>
                                <p class="mt-1 truncate text-sm font-semibold text-slate-500">{{ car.city || 'Bihar' }}</p>
                                <div class="mt-4 grid gap-2 text-center text-[11px] font-black text-slate-600 sm:grid-cols-3">
                                    <span class="rounded-md bg-white px-2 py-2 ring-1 ring-slate-100">{{ car.year || 'N/A' }}</span>
                                    <span class="rounded-md bg-white px-2 py-2 ring-1 ring-slate-100">{{ formatKm(car.km_driven) }}</span>
                                    <span class="rounded-md bg-white px-2 py-2 ring-1 ring-slate-100">{{ formatLabel(car.fuel_type || 'Fuel') }}</span>
                                </div>
                                <div class="mt-4 flex flex-col items-start gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Asking price</p>
                                        <p class="mt-1 text-xl font-black text-slate-950">{{ formatPrice(car.price) }}</p>
                                    </div>
                                    <span class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-black text-white">Details</span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div v-else class="mt-8 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                        <h3 class="text-xl font-black text-slate-950">Inventory is being refreshed</h3>
                        <p class="mt-2 text-sm font-medium text-slate-600">Browse all cars or check again soon for fresh verified listings.</p>
                        <Link href="/cars" class="mt-5 inline-flex rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white">Browse Cars</Link>
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-7xl gap-6 px-4 py-12 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-teal-700">Quick discovery</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">Start with budget, brand or city.</h2>
                    <p class="mt-3 text-sm font-medium leading-7 text-slate-600">
                        Move quickly from broad browsing to cars that match your budget, preferred brand and nearby city.
                    </p>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-lg border border-slate-200 bg-white p-5">
                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-500">By budget</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link
                                v-for="option in budgetOptions"
                                :key="option.value"
                                :href="`/cars?max_price=${option.value}`"
                                class="rounded-md border border-orange-100 bg-orange-50 px-3 py-2 text-sm font-black text-orange-700 transition hover:border-orange-300 hover:bg-white"
                            >
                                {{ option.label }}
                            </Link>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-5">
                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-500">Popular brands</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link
                                v-for="brand in topBrands"
                                :key="brand.id"
                                :href="`/used-${slugify(brand.slug || brand.name)}-cars`"
                                class="rounded-md border border-teal-100 bg-teal-50 px-3 py-2 text-sm font-black text-teal-700 transition hover:border-teal-300 hover:bg-white"
                            >
                                {{ brand.name }}
                            </Link>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-5">
                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-500">Nearby cities</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link
                                v-for="city in topCities"
                                :key="city"
                                :href="`/used-cars-in-${slugify(city)}`"
                                class="rounded-md border border-sky-100 bg-sky-50 px-3 py-2 text-sm font-black text-sky-800 transition hover:border-sky-300 hover:bg-white"
                            >
                                {{ city }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-[#fbfdff]">
                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="lg:col-span-1">
                            <p class="text-sm font-black uppercase tracking-wide text-orange-600">Buying flow</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">A calmer path from search to shortlist.</h2>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3 lg:col-span-2">
                            <div v-for="step in steps" :key="step.title" class="rounded-lg border border-slate-200 bg-white p-5">
                                <p class="text-xs font-black uppercase tracking-wide text-teal-700">{{ step.number }}</p>
                                <h3 class="mt-3 text-lg font-black text-slate-950">{{ step.title }}</h3>
                                <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ step.text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-7xl gap-5 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="rounded-lg border border-orange-100 bg-orange-50 p-6 sm:p-8">
                    <p class="text-sm font-black uppercase tracking-wide text-orange-700">For sellers</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">List your car without making it complicated.</h2>
                    <p class="mt-3 text-sm font-medium leading-7 text-slate-700">
                        Add your car details, verify contact and reach buyers who are already searching in your city.
                    </p>
                    <Link href="/sell-your-car" class="mt-6 inline-flex w-full justify-center rounded-lg bg-orange-500 px-5 py-3 text-sm font-black text-white transition hover:bg-orange-600 sm:w-fit">
                        Sell Your Car
                    </Link>
                </div>

                <div class="rounded-lg border border-teal-100 bg-teal-50 p-6 sm:p-8">
                    <p class="text-sm font-black uppercase tracking-wide text-teal-700">For dealers</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">Bring your inventory into a cleaner showroom.</h2>
                    <p class="mt-3 text-sm font-medium leading-7 text-slate-700">
                        Verified dealer pages, catalog links and enquiry flow help your cars look more trustworthy online.
                    </p>
                    <Link href="/dealer/register" class="mt-6 inline-flex w-full justify-center rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-800 sm:w-fit">
                        Register Dealer
                    </Link>
                </div>
            </section>

            <section class="bg-white">
                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="rounded-lg border border-slate-200 bg-[#f7fbff] p-6 sm:p-8">
                        <p class="text-sm font-black uppercase tracking-wide text-slate-500">Used cars in Bihar</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">SahiGadi helps buyers compare second hand cars with more confidence.</h2>
                        <div class="mt-4 grid gap-4 text-sm font-medium leading-7 text-slate-600 md:grid-cols-2">
                            <p>
                                Explore verified used cars in Patna, Muzaffarpur, Darbhanga and other Bihar cities with filters for budget, brand, fuel type and seller location.
                            </p>
                            <p>
                                Buyers can review seller information, compare shortlists and make confident calls before booking a visit.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, h } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

type HomeCar = {
    id?: number;
    slug?: string;
    title?: string;
    brand?: { id?: number; name?: string; slug?: string } | null;
    model?: string;
    variant?: string;
    year?: number | string;
    fuel_type?: string;
    transmission?: string;
    km_driven?: number | string;
    price?: number | string;
    city?: string;
    is_featured?: boolean;
    is_verified?: boolean;
    image_url?: string;
};

const props = defineProps<{
    featuredCars: HomeCar[];
    latestCars: HomeCar[];
    brands: any[];
    cities: string[];
    budgetOptions: Array<{ label: string; value: string }>;
    fuelTypes: string[];
    plans: any[];
    homepageSchema: string | Record<string, any>;
}>();

const searchForm = useForm({
    keyword: '',
    city: '',
    brand: '',
    max_price: '',
    fuel_type: '',
});

const schemaJson = computed(() => (
    typeof props.homepageSchema === 'string'
        ? props.homepageSchema
        : JSON.stringify(props.homepageSchema)
));

const allCars = computed(() => {
    const seen = new Set<string>();
    return [...(props.featuredCars || []), ...(props.latestCars || [])].filter((car) => {
        const key = String(car.slug || car.id || car.title);
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    });
});

const heroCar = computed(() => allCars.value[0]);
const displayCars = computed(() => allCars.value.slice(0, 8));
const topBrands = computed(() => (props.brands || []).slice(0, 10));
const topCities = computed(() => (props.cities || []).slice(0, 10));
const fullPageRoutes = new Set<string>();

const stats = computed(() => [
    { value: `${new Intl.NumberFormat('en-IN').format(allCars.value.length)}+`, label: 'home picks' },
    { value: `${topCities.value.length}+`, label: 'city filters' },
    { value: `${topBrands.value.length}+`, label: 'brand filters' },
]);

const normalizeOption = (value: string) => value.toLowerCase();

const toggleFuel = (fuel: string) => {
    const normalized = normalizeOption(fuel);
    searchForm.fuel_type = searchForm.fuel_type === normalized ? '' : normalized;
};

const submitSearch = () => {
    searchForm.get('/cars', {
        preserveState: false,
        preserveScroll: false,
    });
};

const carTitle = (car?: HomeCar) => {
    if (!car) return 'Verified used car';
    const brandName = car.brand?.name || '';
    const model = car.model || '';
    return car.title || `${brandName} ${model}`.trim() || 'Used Car';
};

const imageFor = (car?: HomeCar) => {
    const src = car?.image_url || '/images/hero-bg.png';
    if (/^(https?:|\/)/i.test(src)) return src;
    return `/storage/${src}`;
};

const formatPrice = (price?: number | string) => {
    const amount = Number(price || 0);
    if (!amount) return 'Price on request';
    if (amount >= 100000) {
        const lakh = amount / 100000;
        const value = Number.isInteger(lakh) ? lakh.toFixed(0) : lakh.toFixed(1);
        return `₹${value} Lakh`;
    }
    return `₹${new Intl.NumberFormat('en-IN').format(amount)}`;
};

const formatKm = (km?: number | string) => {
    const value = Number(km || 0);
    return value > 0 ? `${new Intl.NumberFormat('en-IN').format(value)} km` : 'KMs N/A';
};

const formatLabel = (value: string) => value ? value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() : 'N/A';

const slugify = (value: string) => String(value || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

const makeIcon = (path: string) => ({
    render() {
        return h('svg', {
            fill: 'none',
            stroke: 'currentColor',
            viewBox: '0 0 24 24',
        }, [
            h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '2',
                d: path,
            }),
        ]);
    },
});

const journeyCards = [
    {
        title: 'Browse verified cars',
        text: 'Shortlist cars with price, photos, seller city and key specs visible upfront.',
        action: 'Explore cars',
        href: '/cars',
        iconClass: 'bg-teal-50 text-teal-700',
        icon: makeIcon('M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'),
    },
    {
        title: 'Sell your car',
        text: 'Create a clean listing and reach buyers already comparing cars in Bihar.',
        action: 'Start selling',
        href: '/sell-your-car',
        iconClass: 'bg-orange-50 text-orange-700',
        icon: makeIcon('M12 4v16m8-8H4'),
    },
    {
        title: 'Find dealers',
        text: 'Browse verified dealer inventories and contact trusted local showrooms.',
        action: 'View dealers',
        href: '/verified-dealers',
        iconClass: 'bg-emerald-50 text-emerald-700',
        icon: makeIcon('M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m4-4h1m-1 4h1'),
    },
];

const steps = [
    {
        number: '01',
        title: 'Search by intent',
        text: 'Start from budget, city, fuel type or brand instead of scrolling through everything.',
    },
    {
        number: '02',
        title: 'Compare the basics',
        text: 'Review price, kilometres, seller type and verification status before opening details.',
    },
    {
        number: '03',
        title: 'Contact with context',
        text: 'Use listing details to ask sharper questions when you speak to the seller.',
    },
];
</script>
