<template>
    <div class="min-h-screen bg-[#f6f8fb] text-slate-950">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <BrandBlock />
            <nav class="flex-1 overflow-y-auto px-4 py-5">
                <NavGroup v-for="group in navGroups" :key="group.title" :group="group" />
            </nav>
        </aside>

        <div v-if="menuOpen" class="fixed inset-0 z-50 lg:hidden">
            <button type="button" class="absolute inset-0 bg-slate-950/60" aria-label="Close dealer menu" @click="menuOpen = false"></button>
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
                            aria-label="Open dealer menu"
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
                        <a
                            v-if="dealer?.slug"
                            :href="`/catalog/${dealer.slug}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hidden rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700 sm:inline-flex"
                        >
                            View catalog
                        </a>
                        <div class="hidden min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 sm:block">
                            <p class="max-w-[180px] truncate text-sm font-black text-slate-900">{{ dealer?.company_name || dealer?.name || 'Dealer' }}</p>
                            <p class="max-w-[180px] truncate text-xs font-semibold capitalize text-slate-500">{{ dealer?.status || 'Account' }}</p>
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

defineProps<{ title: string; eyebrow?: string }>();

type NavItem = { label: string; href: string; match: string[]; icon: any; badge?: () => number };
type NavGroup = { title: string; items: NavItem[] };

const page = usePage();
const menuOpen = ref(false);
const currentUrl = computed(() => String(page.url || '/dealer/dashboard').split('?')[0]);
const dealer = computed(() => (page.props as any).auth?.dealer || null);
const flashSuccess = computed(() => (page.props as any).flash?.success || '');
const flashError = computed(() => (page.props as any).flash?.error || '');

const makeIcon = (path: string) => defineComponent({
    props: { class: { type: String, default: 'h-5 w-5' } },
    setup(iconProps) {
        return () => h('svg', {
            class: iconProps.class,
            fill: 'none',
            stroke: 'currentColor',
            viewBox: '0 0 24 24',
            'aria-hidden': 'true',
        }, [h('path', {
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            d: path,
        })]);
    },
});

const IconMenu = makeIcon('M4 7h16M4 12h16M4 17h16');
const IconDashboard = makeIcon('M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-5H4v5Z');
const IconCar = makeIcon('M8 17h8m-9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm17 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM5 17H3l2-6h14l2 6h-2M7 11l1.7-4.4A2 2 0 0 1 10.56 5h2.88a2 2 0 0 1 1.86 1.26L17 11');
const IconMessage = makeIcon('M21 15a4 4 0 0 1-4 4H7l-4 4v-4a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h14a4 4 0 0 1 4 4v8Z');
const IconWallet = makeIcon('M20 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2ZM16 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2m14 6h.01');
const IconPlan = makeIcon('M12 3l3 6 6 .8-4.5 4.4 1.1 6.3L12 17.5 6.4 20.5l1.1-6.3L3 9.8 9 9l3-6Z');
const IconService = makeIcon('M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.8 2.8-2.1-2.1 2.8-2.8Z');
const IconProfile = makeIcon('M20 21a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z');
const IconLogout = makeIcon('M10 17l5-5-5-5M15 12H3m7 9h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-8');

const navGroups: NavGroup[] = [
    {
        title: 'Showroom',
        items: [
            { label: 'Dashboard', href: '/dealer/dashboard', match: ['/dealer/dashboard'], icon: IconDashboard },
            { label: 'My Inventory', href: '/dealer/cars', match: ['/dealer/cars'], icon: IconCar },
            { label: 'Enquiries', href: '/dealer/enquiries', match: ['/dealer/enquiries'], icon: IconMessage, badge: () => Number(dealer.value?.new_enquiries || 0) },
            { label: 'Wallet', href: '/dealer/wallet', match: ['/dealer/wallet'], icon: IconWallet },
            { label: 'Plans', href: '/dealer/plans', match: ['/dealer/plans'], icon: IconPlan },
        ],
    },
    {
        title: 'Vehicle Services',
        items: [
            { label: 'RC Check', href: '/dealer/vehicle-search', match: ['/dealer/vehicle-search'], icon: IconService },
            { label: 'E-Challans', href: '/dealer/challan-search', match: ['/dealer/challan-search'], icon: IconService },
            { label: 'Challan PDF', href: '/dealer/challan-pdf', match: ['/dealer/challan-pdf'], icon: IconService },
            { label: 'Mahindra History', href: '/dealer/service-history', match: ['/dealer/service-history'], icon: IconService },
            { label: 'Maruti History', href: '/dealer/maruti-service-history', match: ['/dealer/maruti-service-history'], icon: IconService },
        ],
    },
    {
        title: 'Account',
        items: [
            { label: 'My Profile', href: '/dealer/profile', match: ['/dealer/profile'], icon: IconProfile },
            { label: 'Sign Out', href: '/dealer/logout', match: ['/dealer/logout'], icon: IconLogout },
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
                    h('p', { class: 'text-lg font-black leading-none text-slate-950' }, ['SAHI', h('span', { class: 'text-orange-500' }, 'GADI')]),
                    h('p', { class: 'mt-1 text-xs font-bold uppercase tracking-wide text-slate-500' }, 'Dealer portal'),
                ]),
            ]),
            dealer.value ? h('div', { class: 'mt-4 rounded-lg bg-slate-50 p-3' }, [
                h('p', { class: 'truncate text-sm font-black text-slate-900' }, dealer.value.name),
                h('p', { class: 'mt-1 truncate text-xs font-semibold text-slate-500' }, dealer.value.dealer_unique_id || dealer.value.phone),
            ]) : null,
        ]);
    },
});

const NavGroup = defineComponent({
    props: { group: { type: Object as () => NavGroup, required: true } },
    emits: ['navigate'],
    setup(groupProps, { emit }) {
        return () => h('div', { class: 'mb-6' }, [
            h('p', { class: 'mb-2 px-3 text-xs font-black uppercase tracking-wide text-slate-400' }, groupProps.group.title),
            h('div', { class: 'grid gap-1' }, groupProps.group.items.map((item) => h(Link, {
                key: item.href,
                href: item.href,
                class: [
                    'flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 text-sm font-bold transition',
                    isActive(item) ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950',
                ],
                onClick: () => emit('navigate'),
            }, () => [
                h(item.icon, { class: 'h-5 w-5 shrink-0' }),
                h('span', { class: 'min-w-0 flex-1 truncate' }, item.label),
                item.badge && item.badge() > 0
                    ? h('span', { class: 'rounded-full bg-red-100 px-2 py-0.5 text-xs font-black text-red-700' }, item.badge())
                    : null,
            ]))),
        ]);
    },
});
</script>
