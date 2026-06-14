<template>
    <footer class="border-t border-slate-200 bg-[#f8fbff] text-slate-800">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">SahiGadi marketplace</p>
                        <h2 class="mt-2 max-w-2xl text-2xl font-black leading-tight text-slate-950 sm:text-3xl">
                            Browse, sell, and connect with trusted car sellers from one place.
                        </h2>
                        <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-slate-600">
                            A light, transparent used-car experience for buyers, sellers, and dealers across Bihar.
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                        <Link href="/cars" class="inline-flex items-center justify-center rounded-lg bg-teal-700 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-800">
                            Browse Cars
                        </Link>
                        <Link href="/sell-your-car" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-orange-50 px-5 py-3 text-sm font-black text-orange-700 transition hover:border-orange-300 hover:bg-white">
                            Sell Your Car
                        </Link>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 py-10 md:grid-cols-2 lg:grid-cols-[1.2fr_0.85fr_0.9fr_1fr]">
                <div>
                    <Link href="/" class="inline-flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-lg bg-teal-700 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm17 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM5 17H3l2-6h14l2 6h-2M7 11l1.7-4.4A2 2 0 0 1 10.56 5h2.88a2 2 0 0 1 1.86 1.26L17 11" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-2xl font-black tracking-normal text-slate-950">SAHI<span class="text-orange-500">GADI</span></span>
                            <span class="mt-1 block text-[0.62rem] font-bold uppercase tracking-[0.18em] text-slate-500">Verified used cars</span>
                        </span>
                    </Link>
                    <p class="mt-5 max-w-sm text-sm font-medium leading-7 text-slate-600">
                        Verified used cars, dealer catalogs, and owner listings for smarter buying decisions across Bihar.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span v-for="badge in trustBadges" :key="badge" class="rounded-md border border-teal-100 bg-teal-50 px-3 py-2 text-xs font-black text-teal-700">
                            {{ badge }}
                        </span>
                    </div>
                </div>

                <FooterColumn title="Marketplace" :links="marketplaceLinks" />
                <FooterColumn title="Popular Cities" :links="cityLinks" />

                <div>
                    <h2 class="text-sm font-black uppercase tracking-wide text-slate-950">Contact</h2>
                    <div class="mt-5 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <ul class="space-y-3 text-sm font-medium text-slate-600">
                            <li class="flex gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-teal-600"></span>
                                <span>Awani Enterprises, A-5, Sector 65, Noida, UP</span>
                            </li>
                            <li>
                                <a href="mailto:support@sahigadi.com" class="transition hover:text-teal-700">support@sahigadi.com</a>
                            </li>
                            <li>
                                <a href="tel:+919818823408" class="transition hover:text-teal-700">+91 98188 23408</a>
                            </li>
                        </ul>
                        <Link href="/dealer/register" class="mt-5 inline-flex w-full justify-center rounded-lg bg-teal-700 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-800">
                            Register Dealer
                        </Link>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-slate-200 pt-6 text-sm font-semibold text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ new Date().getFullYear() }} SahiGadi. All rights reserved.</p>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    <a href="/privacy-policy" class="transition hover:text-teal-700">Privacy Policy</a>
                    <a href="/terms-of-use" class="transition hover:text-teal-700">Terms of Use</a>
                    <a href="/refund-policy" class="transition hover:text-teal-700">Refund Policy</a>
                    <a href="/sitemap.xml" class="transition hover:text-teal-700">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

const trustBadges = ['Verified listings', 'Seller network', 'Dealer network'];
const fullPageRoutes = new Set(['/privacy-policy', '/terms-of-use', '/refund-policy']);

const marketplaceLinks = [
    { label: 'Buy Used Cars', href: '/cars' },
    { label: 'Sell Your Car', href: '/sell-your-car' },
    { label: 'Verified Dealers', href: '/verified-dealers' },
    { label: 'Contact Support', href: '/contact' },
];

const cityLinks = [
    { label: 'Used Cars in Patna', href: '/used-cars-in-patna' },
    { label: 'Used Cars in Muzaffarpur', href: '/used-cars-in-muzaffarpur' },
    { label: 'Used Cars in Darbhanga', href: '/used-cars-in-darbhanga' },
    { label: 'Used Cars in Gaya', href: '/used-cars-in-gaya' },
];

const FooterColumn = defineComponent({
    props: {
        title: { type: String, required: true },
        links: { type: Array as () => Array<{ label: string; href: string }>, required: true },
    },
    setup(props) {
        return () => h('div', [
            h('h2', { class: 'text-sm font-black uppercase tracking-wide text-slate-950' }, props.title),
            h('ul', { class: 'mt-5 space-y-3 text-sm font-semibold text-slate-600' },
                props.links.map((link) => {
                    const isFullPageRoute = fullPageRoutes.has(link.href);

                    return h('li', [
                        h(
                            isFullPageRoute ? 'a' : Link,
                            { href: link.href, class: 'transition hover:text-teal-700' },
                            isFullPageRoute ? link.label : () => link.label
                        ),
                    ]);
                })
            ),
        ]);
    },
});
</script>
