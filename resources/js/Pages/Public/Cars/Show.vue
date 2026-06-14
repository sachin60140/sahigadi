<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta head-key="description" name="description" :content="seo.description" />
        <meta head-key="keywords" name="keywords" :content="seo.keywords" />
        <meta head-key="og-title" property="og:title" :content="seo.title" />
        <meta head-key="og-description" property="og:description" :content="seo.description" />
        <meta head-key="og-image" property="og:image" :content="car.main_image_url || '/images/car-placeholder.webp'" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large" />
        <link head-key="canonical" rel="canonical" :href="`/car/${car.slug}`" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
        <component is="script" head-key="schema-product" type="application/ld+json" v-html="JSON.stringify(structuredData)"></component>
        <component is="script" head-key="schema-breadcrumb" type="application/ld+json" v-html="JSON.stringify(breadcrumbSchema)"></component>
    </Head>

    <PublicLayout>
        <div class="min-h-screen bg-[#f7fbff] pb-32 lg:pb-12">
            <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f7fbff_0%,#eefbf8_52%,#fff7ed_100%)]">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <nav class="mb-5 flex text-sm font-semibold text-slate-500" aria-label="Breadcrumb">
                        <ol class="inline-flex flex-wrap items-center gap-2">
                            <li class="inline-flex items-center">
                                <Link href="/" class="transition hover:text-teal-700">Home</Link>
                            </li>
                            <li class="text-slate-300">/</li>
                            <li>
                                <Link href="/cars" class="transition hover:text-teal-700">Cars</Link>
                            </li>
                            <li class="text-slate-300">/</li>
                            <li class="max-w-[220px] truncate text-slate-800 sm:max-w-md">{{ car.title }}</li>
                        </ol>
                    </nav>

                    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end">
                        <div>
                            <div class="mb-4 flex flex-wrap gap-2">
                                <span v-if="car.is_verified" class="inline-flex items-center gap-1.5 rounded-lg border border-teal-100 bg-white/80 px-3 py-2 text-xs font-black uppercase tracking-wide text-teal-700 shadow-sm">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016Z" />
                                    </svg>
                                    Verified listing
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-orange-100 bg-white/80 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 shadow-sm">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Fresh details
                                </span>
                            </div>

                            <h1 class="max-w-4xl text-3xl font-black leading-tight tracking-normal text-slate-950 sm:text-4xl lg:text-5xl">
                                {{ car.title }}
                            </h1>
                            <p class="mt-3 max-w-2xl text-base font-medium leading-8 text-slate-600">
                                {{ listingSummary }}
                            </p>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div v-for="item in overviewStats" :key="item.label" class="rounded-lg border border-white bg-white/75 p-3 shadow-sm">
                                    <p class="text-lg font-black text-slate-950">{{ item.value }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ item.label }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70">
                            <p class="text-xs font-black uppercase tracking-wide text-orange-600">Asking price</p>
                            <p class="mt-2 text-3xl font-black text-slate-950">&#8377;{{ formattedPrice }}</p>
                            <p class="mt-2 text-sm font-semibold text-slate-500">{{ locationLabel }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                
                <div class="grid gap-7 lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-8 xl:grid-cols-[minmax(0,1fr)_390px]">
                    <div class="min-w-0 space-y-6">
                        
                        <!-- Image Gallery Component -->
                        <ImageGallery 
                            :images="car.image_urls" 
                            :main-image="car.main_image_url" 
                            :title="car.title"
                            :is-verified="car.is_verified"
                        />

                        <!-- Specs Grid Component -->
                        <SpecsGrid 
                            :year="car.year"
                            :km-driven="car.km_driven"
                            :fuel-type="car.fuel_type"
                            :transmission="car.transmission"
                            :ownership="car.ownership"
                            :registration-number-masked="car.registration_number_masked"
                            :insurance-status="car.insurance_status"
                            :color="car.color"
                            :engine="car.engine"
                            :body-type="car.body_type"
                        />

                        <!-- Description -->
                        <div v-if="car.description" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                            <h2 class="mb-4 text-xl font-black text-slate-950 md:text-2xl">Description</h2>
                            <div class="prose max-w-none whitespace-pre-wrap leading-relaxed text-slate-600" v-html="car.description"></div>
                        </div>

                    </div>

                    <!-- Right Column: Price & Contact Card -->
                    <div class="min-w-0">
                        <PriceContactCard 
                            :title="car.title"
                            :model="car.model"
                            :variant="car.variant"
                            :price="car.price"
                            :city="car.city"
                            :state="car.state"
                            :revealed-contact="revealedContact"
                            @request-contact="isOtpModalOpen = true"
                        />

                        <!-- Seller Info Card Component -->
                        <SellerCard :profile="car.seller_public_profile" />
                    </div>
                </div>

                <!-- Related Cars -->
                <RelatedCars :cars="relatedCars" />
            </div>
        </div>

        <!-- Mobile Sticky CTA -->
        <MobileStickyCTA 
            :revealed-contact="revealedContact"
            @request-contact="isOtpModalOpen = true"
        />

        <!-- OTP Contact Unlock Modal Component -->
        <OtpUnlockModal 
            :is-open="isOtpModalOpen"
            :car-id="car.isCustomerListing ? null : car.id"
            :customer-car-listing-id="car.isCustomerListing ? car.id : null"
            @close="isOtpModalOpen = false"
            @verified="handleContactVerified"
        />
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ImageGallery from '@/Components/Public/ImageGallery.vue';
import SpecsGrid from '@/Components/Public/SpecsGrid.vue';
import PriceContactCard from '@/Components/Public/PriceContactCard.vue';
import SellerCard from '@/Components/Public/SellerCard.vue';
import OtpUnlockModal from '@/Components/Public/OtpUnlockModal.vue';
import RelatedCars from '@/Components/Public/RelatedCars.vue';
import MobileStickyCTA from '@/Components/Public/MobileStickyCTA.vue';

const props = defineProps<{
    car: any;
    relatedCars: any[];
    seo: any;
    structuredData: any;
    breadcrumbSchema: any;
}>();

const isOtpModalOpen = ref(false);
const revealedContact = ref<string | null>(null);

const formatLabel = (value: string | number | null | undefined) => value ? String(value) : 'N/A';

const formattedPrice = computed(() => new Intl.NumberFormat('en-IN', {
    maximumFractionDigits: 0,
}).format(Number(props.car.price || 0)));

const formattedKm = computed(() => {
    const km = Number(props.car.km_driven || 0);
    return km > 0 ? `${new Intl.NumberFormat('en-IN').format(km)} km` : 'KMs N/A';
});

const locationLabel = computed(() => {
    const city = props.car.city || 'Bihar';
    return props.car.state ? `${city}, ${props.car.state}` : city;
});

const listingSummary = computed(() => [
    props.car.year,
    props.car.brand,
    props.car.model,
    props.car.variant,
].filter(Boolean).join(' ') || 'Used car listing from SahiGadi');

const overviewStats = computed(() => [
    { label: 'make year', value: formatLabel(props.car.year) },
    { label: 'kilometres', value: formattedKm.value },
    { label: 'fuel and gear', value: [props.car.fuel_type, props.car.transmission].filter(Boolean).join(' / ') || 'N/A' },
]);

const handleContactVerified = (contactNumber: string) => {
    revealedContact.value = contactNumber;
};
</script>
