<template>
    <div class="min-h-screen bg-[#f7f9fb] text-slate-950">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <BrandBlock />
            <nav class="flex-1 overflow-y-auto px-4 py-5">
                <NavGroup v-for="group in navGroups" :key="group.title" :group="group" />
            </nav>
        </aside>

        <div v-if="menuOpen" class="fixed inset-0 z-50 lg:hidden">
            <button type="button" class="absolute inset-0 bg-slate-950/60" aria-label="Close customer menu" @click="menuOpen = false"></button>
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
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                            aria-label="Open customer menu"
                            @click="menuOpen = true"
                        >
                            <Menu class="h-5 w-5" />
                        </button>
                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-wide text-teal-700">{{ eyebrow }}</p>
                            <h1 class="truncate text-xl font-black text-slate-950 sm:text-2xl">{{ title }}</h1>
                        </div>
                    </div>
                    <div class="hidden items-center gap-3 sm:flex">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-right">
                            <p class="text-xs font-bold text-slate-500">Wallet balance</p>
                            <p class="text-sm font-black text-teal-700">{{ money(customer?.wallet_balance) }}</p>
                        </div>
                        <Link href="/" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50">
                            Marketplace
                        </Link>
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
import {
    CarFront,
    FileText,
    Gauge,
    History,
    LogOut,
    Menu,
    MessageSquareText,
    Search,
    UserRound,
    WalletCards,
} from '@lucide/vue';

defineProps<{ title: string; eyebrow?: string }>();

type NavItem = { label: string; href: string; match: string[]; icon: any; method?: 'post' };
type NavGroup = { title: string; items: NavItem[] };

const page = usePage();
const menuOpen = ref(false);
const currentUrl = computed(() => String(page.url || '/customer/dashboard').split('?')[0]);
const customer = computed(() => (page.props as any).auth?.customer || null);
const flashSuccess = computed(() => (page.props as any).flash?.success || '');
const flashError = computed(() => (page.props as any).flash?.error || '');
const money = (value: number | undefined) => `Rs ${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const navGroups: NavGroup[] = [
    {
        title: 'My Account',
        items: [
            { label: 'Dashboard', href: '/customer/dashboard', match: ['/customer/dashboard'], icon: Gauge },
            { label: 'Enquiries', href: '/customer/enquiries', match: ['/customer/enquiries'], icon: MessageSquareText },
            { label: 'My Wallet', href: '/customer/wallet', match: ['/customer/wallet', '/customer/payments'], icon: WalletCards },
            { label: 'My Profile', href: '/customer/profile', match: ['/customer/profile'], icon: UserRound },
        ],
    },
    {
        title: 'Vehicle Services',
        items: [
            { label: 'Sell a Car', href: '/sell-your-car', match: ['/sell-your-car'], icon: CarFront },
            { label: 'RC Search', href: '/vehicle-search', match: ['/vehicle-search'], icon: Search },
            { label: 'Mahindra History', href: '/mahindra-service-history', match: ['/mahindra-service-history'], icon: History },
            { label: 'Maruti History', href: '/maruti-service-history', match: ['/maruti-service-history'], icon: History },
            { label: 'Challan PDF', href: '/customer/challan-pdf', match: ['/customer/challan-pdf'], icon: FileText },
        ],
    },
    {
        title: 'Session',
        items: [
            { label: 'Sign Out', href: '/customer/logout', match: ['/customer/logout'], icon: LogOut, method: 'post' },
        ],
    },
];

const isActive = (item: NavItem) => item.match.some((path) => currentUrl.value === path || currentUrl.value.startsWith(`${path}/`));

const BrandBlock = defineComponent({
    setup() {
        return () => h('div', { class: 'border-b border-slate-200 px-5 py-5' }, [
            h(Link, { href: '/', class: 'flex items-center gap-3' }, () => [
                h('span', { class: 'grid h-11 w-11 place-items-center rounded-lg bg-teal-700 text-white shadow-sm' }, [
                    h(CarFront, { class: 'h-5 w-5' }),
                ]),
                h('div', { class: 'min-w-0' }, [
                    h('p', { class: 'text-lg font-black leading-none text-slate-950' }, ['SAHI', h('span', { class: 'text-orange-500' }, 'GADI')]),
                    h('p', { class: 'mt-1 text-xs font-bold uppercase tracking-wide text-slate-500' }, 'Customer account'),
                ]),
            ]),
            customer.value ? h('div', { class: 'mt-4 flex items-center gap-3 rounded-lg bg-slate-50 p-3' }, [
                customer.value.profile_image_url
                    ? h('img', { src: customer.value.profile_image_url, alt: '', class: 'h-11 w-11 rounded-full object-cover' })
                    : h('span', { class: 'grid h-11 w-11 place-items-center rounded-full bg-white text-slate-500 ring-1 ring-slate-200' }, [
                        h(UserRound, { class: 'h-5 w-5' }),
                    ]),
                h('div', { class: 'min-w-0' }, [
                    h('p', { class: 'truncate text-sm font-black text-slate-900' }, customer.value.name || 'Customer'),
                    h('p', { class: 'mt-1 truncate text-xs font-semibold text-slate-500' }, customer.value.customer_unique_id || customer.value.phone),
                ]),
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
                method: item.method,
                as: item.method ? 'button' : 'a',
                class: [
                    'flex min-h-11 w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-bold transition',
                    isActive(item) ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950',
                ],
                onClick: () => emit('navigate'),
            }, () => [
                h(item.icon, { class: 'h-5 w-5 shrink-0' }),
                h('span', { class: 'min-w-0 flex-1 truncate' }, item.label),
            ]))),
        ]);
    },
});
</script>
