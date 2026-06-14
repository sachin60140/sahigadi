<template>
    <div class="min-h-screen bg-[#f6f8fb] text-slate-950">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <BrandBlock />
            <nav class="flex-1 overflow-y-auto px-4 py-5">
                <NavGroup v-for="group in navGroups" :key="group.title" :group="group" />
            </nav>
        </aside>

        <div v-if="menuOpen" class="fixed inset-0 z-50 lg:hidden">
            <button class="absolute inset-0 bg-slate-950/60" type="button" aria-label="Close admin menu" @click="menuOpen = false"></button>
            <aside class="relative flex h-full w-[min(88vw,320px)] flex-col border-r border-slate-200 bg-white shadow-2xl">
                <BrandBlock />
                <nav class="flex-1 overflow-y-auto px-4 py-5">
                    <NavGroup v-for="group in navGroups" :key="group.title" :group="group" @navigate="menuOpen = false" />
                </nav>
            </aside>
        </div>

        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
                <div class="flex min-h-[72px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                            aria-label="Open admin menu"
                            @click="menuOpen = true"
                        >
                            <IconMenu class="h-5 w-5" />
                        </button>
                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-wide text-teal-700">{{ eyebrow }}</p>
                            <h1 class="truncate text-xl font-black text-slate-950 sm:text-2xl">{{ title }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            href="/"
                            class="hidden rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700 sm:inline-flex"
                        >
                            Public Site
                        </Link>
                        <div class="hidden min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 sm:block">
                            <p class="max-w-[180px] truncate text-sm font-black text-slate-900">{{ adminName }}</p>
                            <p class="max-w-[180px] truncate text-xs font-semibold text-slate-500">{{ adminEmail }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div v-if="flashSuccess" class="mb-5 rounded-lg border border-teal-100 bg-teal-50 px-4 py-3 text-sm font-bold text-teal-800">
                    {{ flashSuccess }}
                </div>
                <div v-if="flashError" class="mb-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    {{ flashError }}
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    title: string;
    eyebrow?: string;
}>();

type NavItem = {
    label: string;
    href: string;
    match: string[];
    icon: any;
};

type NavGroup = {
    title: string;
    items: NavItem[];
};

const page = usePage();
const menuOpen = ref(false);

const currentUrl = computed(() => String(page.url || '/admin/dashboard').split('?')[0]);
const adminName = computed(() => (page.props as any).auth?.admin?.name || 'Admin');
const adminEmail = computed(() => (page.props as any).auth?.admin?.email || 'admin@sahigadi.com');
const flashSuccess = computed(() => (page.props as any).flash?.success || '');
const flashError = computed(() => (page.props as any).flash?.error || '');

const makeIcon = (path: string) => defineComponent({
    props: { class: { type: String, default: 'h-5 w-5' } },
    setup(props) {
        return () => h('svg', {
            class: props.class,
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

const IconMenu = makeIcon('M4 7h16M4 12h16M4 17h16');
const IconDashboard = makeIcon('M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-5H4v5Z');
const IconUsers = makeIcon('M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75');
const IconCar = makeIcon('M8 17h8m-9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm17 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM5 17H3l2-6h14l2 6h-2M7 11l1.7-4.4A2 2 0 0 1 10.56 5h2.88a2 2 0 0 1 1.86 1.26L17 11');
const IconWallet = makeIcon('M20 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2ZM16 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2m14 6h.01');
const IconPayment = makeIcon('M3 7h18M3 10h18M7 15h4m-8 4h18a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z');
const IconSettings = makeIcon('M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5ZM19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.92 4a1.65 1.65 0 0 0 1-1.51V2a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.14.31.23.65.23 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z');
const IconService = makeIcon('M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.8 2.8-2.1-2.1 2.8-2.8Z');
const IconMessage = makeIcon('M21 15a4 4 0 0 1-4 4H7l-4 4v-4a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h14a4 4 0 0 1 4 4v8Z');
const IconLogout = makeIcon('M10 17l5-5-5-5M15 12H3m7 9h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-8');

const navGroups: NavGroup[] = [
    {
        title: 'Command',
        items: [
            { label: 'Dashboard', href: '/admin/dashboard', match: ['/admin/dashboard'], icon: IconDashboard },
            { label: 'Dealers', href: '/admin/dealers', match: ['/admin/dealers'], icon: IconUsers },
            { label: 'Customers', href: '/admin/customers', match: ['/admin/customers'], icon: IconUsers },
            { label: 'Cars', href: '/admin/cars', match: ['/admin/cars'], icon: IconCar },
            { label: 'Customer Listings', href: '/admin/customer-listings', match: ['/admin/customer-listings'], icon: IconCar },
        ],
    },
    {
        title: 'Revenue',
        items: [
            { label: 'Dealer Wallets', href: '/admin/wallet-recharges', match: ['/admin/wallet-recharges'], icon: IconWallet },
            { label: 'Customer Wallets', href: '/admin/customer-wallet-recharges', match: ['/admin/customer-wallet-recharges'], icon: IconWallet },
            { label: 'Payment Links', href: '/admin/payment-links', match: ['/admin/payment-links'], icon: IconPayment },
            { label: 'Payments & Refunds', href: '/admin/customer-transactions', match: ['/admin/customer-transactions'], icon: IconPayment },
        ],
    },
    {
        title: 'Operations',
        items: [
            { label: 'Enquiries', href: '/admin/enquiries', match: ['/admin/enquiries'], icon: IconMessage },
            { label: 'Contact Enquiries', href: '/admin/contact-enquiries', match: ['/admin/contact-enquiries'], icon: IconMessage },
            { label: 'RC Searches', href: '/admin/vehicle-searches', match: ['/admin/vehicle-searches', '/admin/customer-vehicle-searches', '/admin/service-tracking/vehicle-search'], icon: IconService },
            { label: 'Service History', href: '/admin/service-histories', match: ['/admin/service-histories', '/admin/maruti-service-histories', '/admin/customer-maruti-service-histories', '/admin/mahindra-service-histories', '/admin/service-tracking/service-history'], icon: IconService },
            { label: 'E-Challan', href: '/admin/challan-searches', match: ['/admin/challan-searches', '/admin/service-tracking/challan-search'], icon: IconService },
            { label: 'Challan PDF', href: '/admin/challan-pdf', match: ['/admin/challan-pdf'], icon: IconService },
        ],
    },
    {
        title: 'Settings',
        items: [
            { label: 'Plans', href: '/admin/plans', match: ['/admin/plans', '/admin/featured-plans'], icon: IconSettings },
            { label: 'Brands', href: '/admin/brands', match: ['/admin/brands'], icon: IconSettings },
            { label: 'Payment Settings', href: '/admin/payment-settings', match: ['/admin/payment-settings'], icon: IconSettings },
            { label: 'Change Password', href: '/admin/change-password', match: ['/admin/change-password'], icon: IconSettings },
            { label: 'Logout', href: '/admin/logout', match: ['/admin/logout'], icon: IconLogout },
        ],
    },
];

const isActive = (item: NavItem) => item.match.some((path) => currentUrl.value === path || currentUrl.value.startsWith(`${path}/`));

const BrandBlock = defineComponent({
    setup() {
        return () => h('div', { class: 'border-b border-slate-200 px-5 py-5' }, [
            h('div', { class: 'flex items-center gap-3' }, [
                h('span', { class: 'grid h-11 w-11 place-items-center rounded-lg bg-teal-700 text-white shadow-sm' }, [
                    h(IconCar, { class: 'h-5 w-5' }),
                ]),
                h('div', { class: 'min-w-0' }, [
                    h('p', { class: 'text-lg font-black leading-none text-slate-950' }, [
                        'SAHI',
                        h('span', { class: 'text-orange-500' }, 'GADI'),
                    ]),
                    h('p', { class: 'mt-1 text-xs font-bold uppercase tracking-wide text-slate-500' }, 'Admin operations'),
                ]),
            ]),
        ]);
    },
});

const NavGroup = defineComponent({
    props: {
        group: { type: Object as () => NavGroup, required: true },
    },
    emits: ['navigate'],
    setup(props, { emit }) {
        return () => h('div', { class: 'mb-6' }, [
            h('p', { class: 'mb-2 px-3 text-xs font-black uppercase tracking-wide text-slate-400' }, props.group.title),
            h('div', { class: 'grid gap-1' }, props.group.items.map((item) => h(Link, {
                key: item.href,
                href: item.href,
                class: [
                    'flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 text-sm font-bold transition',
                    isActive(item)
                        ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950',
                ],
                onClick: () => emit('navigate'),
            }, () => [
                h(item.icon, { class: 'h-5 w-5 shrink-0' }),
                h('span', { class: 'truncate' }, item.label),
            ]))),
        ]);
    },
});
</script>
