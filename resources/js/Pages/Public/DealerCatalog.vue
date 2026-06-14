<template>
    <Head>
        <title>{{ seoTitle || `${dealer.name} - Car Listings | SAHI GADI` }}</title>
        <meta head-key="description" name="description" :content="seoDescription || `Browse verified pre-owned cars from ${dealer.name}.`" />
        <meta head-key="keywords" name="keywords" :content="`${dealer.name} used cars, verified dealer inventory, used cars from dealer, SAHI GADI dealer catalog`" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
        <link head-key="canonical" rel="canonical" :href="`/catalog/${dealer.slug}`" />
        <meta head-key="og-title" property="og:title" :content="seoTitle || `${dealer.name} - Car Listings | SAHI GADI`" />
        <meta head-key="og-description" property="og:description" :content="seoDescription || `Browse verified pre-owned cars from ${dealer.name}.`" />
        <meta v-if="ogImage" head-key="og-image" property="og:image" :content="ogImage" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
        <component is="script" head-key="schema-dealer" type="application/ld+json" v-html="schemaJson"></component>
    </Head>

    <PublicLayout>
        <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f7fbff_0%,#eefbf8_52%,#fff7ed_100%)]">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <nav class="mb-6 flex text-sm font-semibold text-slate-500" aria-label="Breadcrumb">
                    <ol class="inline-flex flex-wrap items-center gap-2">
                        <li><Link href="/" class="transition hover:text-teal-700">Home</Link></li>
                        <li class="text-slate-300">/</li>
                        <li><Link href="/verified-dealers" class="transition hover:text-teal-700">Verified Dealers</Link></li>
                        <li class="text-slate-300">/</li>
                        <li class="font-black text-teal-700">{{ dealer.name }}</li>
                    </ol>
                </nav>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-end">
                    <div>
                        <div class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg border border-teal-100 bg-white/85 px-3 py-2 text-xs font-black uppercase tracking-wide text-teal-700 shadow-sm">
                            <IconShield class="h-4 w-4" />
                            SahiGadi verified dealer
                        </div>
                        <h1 class="max-w-4xl text-3xl font-black leading-tight tracking-normal text-slate-950 sm:text-5xl">
                            {{ dealer.name }} car listings
                        </h1>
                        <p class="mt-5 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                            Browse {{ formatNumber(cars.total || 0) }} verified pre-owned cars from this dealer and compare inventory before you call.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <TrustChip label="Verified inventory"><IconCheck class="h-4 w-4" /></TrustChip>
                            <TrustChip v-if="dealer.city" :label="dealer.city"><IconLocation class="h-4 w-4" /></TrustChip>
                            <TrustChip :label="`${formatNumber(cars.total || 0)} listings`"><IconCar class="h-4 w-4" /></TrustChip>
                        </div>
                    </div>

                    <DealerSummary :dealer="dealer" :total="cars.total || 0" />
                </div>
            </div>
        </section>

        <section class="bg-[#f7fbff] py-10 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-7 lg:grid-cols-[310px_minmax(0,1fr)]">
                    <aside class="lg:sticky lg:top-24 lg:self-start">
                        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="grid h-16 w-16 place-items-center rounded-lg bg-teal-700 text-white shadow-lg shadow-teal-700/15">
                                <IconShop class="h-8 w-8" />
                            </div>
                            <h2 class="mt-4 text-2xl font-black text-slate-950">{{ dealer.name }}</h2>
                            <p v-if="dealer.company_name" class="mt-1 text-sm font-bold text-slate-500">{{ dealer.company_name }}</p>

                            <div class="mt-5 grid gap-3 text-sm font-semibold text-slate-600">
                                <a v-if="dealer.phone" :href="`tel:${dealer.phone}`" class="flex items-center gap-2 transition hover:text-teal-700">
                                    <IconPhone class="h-4 w-4 text-orange-500" />
                                    {{ dealer.phone }}
                                </a>
                                <a v-if="dealer.email" :href="`mailto:${dealer.email}`" class="flex items-center gap-2 transition hover:text-teal-700">
                                    <IconMail class="h-4 w-4 text-orange-500" />
                                    {{ dealer.email }}
                                </a>
                                <p v-if="dealer.city" class="flex items-center gap-2">
                                    <IconLocation class="h-4 w-4 text-orange-500" />
                                    {{ dealer.city }}
                                </p>
                            </div>

                            <div class="mt-5 border-t border-slate-200 pt-5">
                                <span class="inline-flex items-center gap-2 rounded-md bg-orange-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 ring-1 ring-orange-100">
                                    <IconCar class="h-4 w-4" />
                                    {{ formatNumber(cars.total || 0) }} listings
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 rounded-lg border border-teal-100 bg-teal-50 p-5">
                            <div class="flex items-start gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-white text-teal-700">
                                    <IconShield class="h-5 w-5" />
                                </span>
                                <div>
                                    <h3 class="text-sm font-black text-slate-950">Verified dealer</h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-slate-600">
                                        Listings from this dealer are reviewed by SahiGadi for marketplace trust and authenticity.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <main class="min-w-0">
                        <div class="mb-5 flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Showing</p>
                                <h2 class="text-xl font-black text-slate-950">{{ resultSummary }}</h2>
                                <p class="mt-1 text-sm font-medium text-slate-500">Sort this dealer's inventory by price, newest or kilometres.</p>
                            </div>
                            <SortDropdown
                                v-model="sortValue"
                                :options="sortOptions"
                                @update:modelValue="applySort"
                            />
                        </div>

                        <div v-if="cars.data.length" class="grid grid-cols-1 gap-5 sm:grid-cols-[repeat(auto-fill,minmax(270px,1fr))]">
                            <CarCard v-for="car in cars.data" :key="car.id" :car="car" />
                        </div>

                        <div v-else class="rounded-lg border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-teal-50 text-teal-700">
                                <IconCar class="h-8 w-8" />
                            </span>
                            <h2 class="mt-5 text-2xl font-black text-slate-950">No cars available</h2>
                            <p class="mx-auto mt-3 max-w-xl text-sm font-medium leading-7 text-slate-600">
                                This dealer currently has no active listings. Browse all cars or check again later.
                            </p>
                            <Link href="/cars" class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-800">
                                Browse All Cars
                                <IconArrow class="h-4 w-4" />
                            </Link>
                        </div>

                        <PaginationLinks v-if="cars.links?.length > 3" :links="cars.links" />
                    </main>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref, useSlots, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import CarCard from '@/Components/Public/CarCard.vue';
import SortDropdown from '@/Components/Public/SortDropdown.vue';

type Dealer = {
    id: number;
    name: string;
    company_name?: string | null;
    slug: string;
    phone?: string | null;
    email?: string | null;
    city?: string | null;
    gst_verified: boolean;
    email_verified: boolean;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedCars = {
    data: any[];
    total: number;
    links: PaginationLink[];
};

const props = defineProps<{
    dealer: Dealer;
    cars: PaginatedCars;
    seoTitle?: string;
    seoDescription?: string;
    ogImage?: string | null;
    filters?: { sort?: string };
    sortOptions: Array<{ label: string; value: string }>;
}>();

const sortValue = ref(props.filters?.sort || 'relevance');

watch(() => props.filters?.sort, (value) => {
    sortValue.value = value || 'relevance';
});

const formatNumber = (value: number | string) => new Intl.NumberFormat('en-IN').format(Number(value || 0));

const resultSummary = computed(() => {
    const total = Number(props.cars?.total || 0);
    if (!total) return 'No active cars';
    return `${formatNumber(total)} verified ${total === 1 ? 'car' : 'cars'}`;
});

const applySort = (value: string) => {
    router.get(`/catalog/${props.dealer.slug}`, { sort: value === 'relevance' ? undefined : value }, {
        preserveScroll: true,
        preserveState: false,
    });
};

const schemaJson = computed(() => JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: `${props.dealer.name} Car Listings | SAHI GADI`,
    description: props.seoDescription || `Browse verified pre-owned cars from ${props.dealer.name}.`,
    url: `/catalog/${props.dealer.slug}`,
    mainEntity: {
        '@type': 'ItemList',
        numberOfItems: props.cars.data.length,
        itemListElement: props.cars.data.map((car, index) => ({
            '@type': 'ListItem',
            position: index + 1,
            name: car.title,
            url: `/car/${car.slug}`,
        })),
    },
}));

const makeIcon = (path: string) => defineComponent({
    props: { class: { type: String, default: 'h-5 w-5' } },
    setup(iconProps) {
        return () => h('svg', {
            class: iconProps.class,
            fill: 'none',
            stroke: 'currentColor',
            viewBox: '0 0 24 24',
            'aria-hidden': 'true',
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

const IconShield = makeIcon('M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016Z');
const IconCheck = makeIcon('M5 13l4 4L19 7');
const IconCar = makeIcon('M8 17h8m-9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm17 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM5 17H3l2-6h14l2 6h-2M7 11l1.7-4.4A2 2 0 0 1 10.56 5h2.88a2 2 0 0 1 1.86 1.26L17 11');
const IconLocation = makeIcon('M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11ZM12 10.5h.01');
const IconShop = makeIcon('M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m4-4h1m-1 4h1');
const IconPhone = makeIcon('M2 5.5C2 4.12 3.12 3 4.5 3h2A1.5 1.5 0 0 1 8 4.28l.4 2.4a1.5 1.5 0 0 1-.43 1.32l-.9.9a12 12 0 0 0 5.03 5.03l.9-.9a1.5 1.5 0 0 1 1.32-.43l2.4.4A1.5 1.5 0 0 1 18 16.5v2A2.5 2.5 0 0 1 15.5 21h-.5C7.82 21 2 15.18 2 8v-.5Z');
const IconMail = makeIcon('M3 8l8.4 5.6a1 1 0 0 0 1.2 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z');
const IconArrow = makeIcon('M17 8l4 4m0 0-4 4m4-4H3');

const TrustChip = defineComponent({
    props: { label: { type: String, required: true } },
    setup(chipProps) {
        const slots = useSlots();
        return () => h('span', { class: 'inline-flex items-center gap-2 rounded-md border border-teal-100 bg-teal-50 px-3 py-2 text-xs font-black text-teal-700' }, [
            slots.default?.(),
            chipProps.label,
        ]);
    },
});

const DealerSummary = defineComponent({
    props: {
        dealer: { type: Object as () => Dealer, required: true },
        total: { type: Number, required: true },
    },
    setup(summaryProps) {
        return () => h('div', { class: 'rounded-lg border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70' }, [
            h('p', { class: 'text-xs font-black uppercase tracking-wide text-orange-600' }, 'Dealer profile'),
            h('h2', { class: 'mt-2 text-2xl font-black text-slate-950' }, summaryProps.dealer.company_name || summaryProps.dealer.name),
            h('p', { class: 'mt-3 text-sm font-medium leading-7 text-slate-600' }, 'Review dealer contact details and inventory count before opening a listing.'),
            h('div', { class: 'mt-5 grid gap-3 sm:grid-cols-2' }, [
                h('div', { class: 'rounded-lg border border-slate-200 bg-slate-50 p-4' }, [
                    h('p', { class: 'text-2xl font-black text-slate-950' }, formatNumber(summaryProps.total)),
                    h('p', { class: 'mt-2 text-sm font-bold text-slate-500' }, 'active cars'),
                ]),
                h('div', { class: 'rounded-lg border border-slate-200 bg-slate-50 p-4' }, [
                    h('p', { class: 'text-2xl font-black text-slate-950' }, summaryProps.dealer.gst_verified ? 'GST' : 'Email'),
                    h('p', { class: 'mt-2 text-sm font-bold text-slate-500' }, 'verified'),
                ]),
            ]),
        ]);
    },
});

const PaginationLinks = defineComponent({
    props: {
        links: { type: Array as () => PaginationLink[], required: true },
    },
    setup(paginationProps) {
        return () => h('div', { class: 'mt-8 flex flex-wrap justify-center gap-2' }, paginationProps.links.map((link, index) => {
            const classes = [
                'rounded-lg border px-3 py-2 text-sm font-bold leading-4 transition sm:px-4',
                link.active
                    ? 'border-teal-700 bg-teal-700 text-white'
                    : 'border-slate-200 bg-white text-slate-700 hover:bg-teal-700 hover:text-white',
                !link.url ? 'pointer-events-none text-slate-400 hover:bg-white hover:text-slate-400' : '',
            ];

            return link.url
                ? h(Link, { key: index, href: link.url, class: classes, innerHTML: link.label })
                : h('span', { key: index, class: classes, innerHTML: link.label });
        }));
    },
});
</script>
