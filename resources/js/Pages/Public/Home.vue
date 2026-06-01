<template>
    <Head>
        <title>SahiGadi - Verified Used Cars in Bihar</title>
        <meta name="description" content="Buy, sell and verify pre-owned cars in Bihar. SahiGadi is the most trusted marketplace for used cars in Muzaffarpur, Patna, Darbhanga and more." />
        <meta name="keywords" content="used cars in bihar, second hand cars muzaffarpur, buy used cars patna, sahigadi" />
        
        <!-- JSON-LD Schema -->
        <script type="application/ld+json" v-html="homepageSchema"></script>
    </Head>

    <PublicLayout>
        <!-- Hero Section & Search -->
        <HeroSection 
            :cities="cities" 
            :brands="brands" 
            :budget-options="budgetOptions" 
            :fuel-types="fuelTypes" 
        />
        
        <!-- Trust Badges -->
        <TrustBadges />
        
        <!-- Featured Cars Section -->
        <FeaturedCars :cars="featuredCars" />

        <!-- Recently Added Cars Section -->
        <LatestCars :cars="latestCars" />
        
        <!-- Browse By Budget -->
        <BrowseByBudget />
        
        <!-- Browse By Brand -->
        <BrowseByBrand :brands="brands" />
        
        <!-- Browse By City -->
        <BrowseByCity />
        
        <!-- Sell Car CTA -->
        <SellCarCTA />

        <!-- Dealer CTA -->
        <DealerCTA />

        <!-- Why Choose Us -->
        <WhyChooseUs />
        
        <!-- FAQ Section -->
        <FaqSection />
        
        <!-- SEO Content Block -->
        <SeoContentBlock />
        
    </PublicLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

import HeroSection from '@/Components/Public/HeroSection.vue';
import TrustBadges from '@/Components/Public/TrustBadges.vue';
import FeaturedCars from '@/Components/Public/FeaturedCars.vue';
import LatestCars from '@/Components/Public/LatestCars.vue';
import BrowseByBudget from '@/Components/Public/BrowseByBudget.vue';
import SellCarCTA from '@/Components/Public/SellCarCTA.vue';
import BrowseByBrand from '@/Components/Public/BrowseByBrand.vue';
import BrowseByCity from '@/Components/Public/BrowseByCity.vue';
import WhyChooseUs from '@/Components/Public/WhyChooseUs.vue';
import DealerCTA from '@/Components/Public/DealerCTA.vue';
import FaqSection from '@/Components/Public/FaqSection.vue';
import SeoContentBlock from '@/Components/Public/SeoContentBlock.vue';

const props = defineProps<{
    featuredCars: any[];
    latestCars: any[];
    brands: any[];
    cities: string[];
    budgetOptions: any[];
    fuelTypes: string[];
    plans: any[];
    homepageSchema: string;
}>();
</script>
