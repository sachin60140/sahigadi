<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta name="description" :content="seo.description" />
        <meta name="keywords" :content="seo.keywords" />
        <meta property="og:title" :content="seo.title" />
        <meta property="og:description" :content="seo.description" />
        <meta property="og:image" :content="car.main_image_url || '/images/car-placeholder.webp'" />
        <script type="application/ld+json" v-html="JSON.stringify(structuredData)"></script>
        <script type="application/ld+json" v-html="JSON.stringify(breadcrumbSchema)"></script>
    </Head>

    <PublicLayout>
        <div class="bg-gray-50 min-h-screen py-8 pb-32 lg:pb-12">
            <div class="max-w-[94%] xl:max-w-[1400px] mx-auto">
                
                <!-- Breadcrumbs -->
                <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <Link href="/" class="hover:text-[#E30613] transition font-medium">Home</Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <Link href="/cars" class="hover:text-[#E30613] transition font-medium">Cars</Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-[#071226] font-bold truncate max-w-[200px] md:max-w-[400px]">{{ car.title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                    <!-- Left Column: Gallery & Details -->
                    <div class="w-full lg:w-[65%] xl:w-[68%] space-y-8">
                        
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
                        <div v-if="car.description" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <h2 class="text-xl md:text-2xl font-bold text-[#071226] mb-4">Description</h2>
                            <div class="prose max-w-none text-gray-600 leading-relaxed whitespace-pre-wrap" v-html="car.description"></div>
                        </div>

                    </div>

                    <!-- Right Column: Price & Contact Card -->
                    <div class="w-full lg:w-[35%] xl:w-[32%]">
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
import { ref } from 'vue';
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

const handleContactVerified = (contactNumber: string) => {
    revealedContact.value = contactNumber;
};
</script>
