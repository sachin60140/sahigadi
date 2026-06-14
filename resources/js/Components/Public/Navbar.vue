<template>
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <Link href="/" class="flex min-w-0 items-center gap-3" aria-label="SahiGadi home">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-teal-700 text-white shadow-sm sm:h-11 sm:w-11">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm17 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM5 17H3l2-6h14l2 6h-2M7 11l1.7-4.4A2 2 0 0 1 10.56 5h2.88a2 2 0 0 1 1.86 1.26L17 11" />
                    </svg>
                </span>
                <span class="min-w-0 leading-none">
                    <span class="block text-lg font-black tracking-normal text-slate-950 sm:text-xl">SAHI<span class="text-orange-500">GADI</span></span>
                    <span class="mt-1 block max-w-[138px] truncate text-[0.58rem] font-bold uppercase tracking-[0.14em] text-slate-500 sm:max-w-none sm:text-[0.62rem] sm:tracking-[0.16em]">Verified used cars</span>
                </span>
            </Link>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                <NavLink href="/" label="Home" :active="currentUrl === '/'" />
                <NavLink href="/cars" label="Buy Car" :active="isCarsActive" />
                <NavLink href="/sell-your-car" label="Sell Car" :active="currentUrl === '/sell-your-car'" />
                <NavLink href="/verified-dealers" label="Dealers" :active="isDealersActive" />
                <NavLink href="/contact" label="Contact" :active="currentUrl === '/contact'" />
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <template v-if="auth.customer">
                    <Link href="/customer/dashboard" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                        Dashboard
                    </Link>
                </template>
                <template v-else-if="auth.dealer">
                    <Link href="/dealer/dashboard" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                        Dealer Panel
                    </Link>
                </template>
                <template v-else>
                    <Link href="/customer/login" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                        Login
                    </Link>
                    <Link href="/dealer/register" class="rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-lg">
                        Register Dealer
                    </Link>
                </template>
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                :aria-expanded="showingNavigationDropdown"
                aria-label="Toggle navigation"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
            >
                <svg v-if="!showingNavigationDropdown" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div v-show="showingNavigationDropdown" class="border-t border-slate-200 bg-white lg:hidden">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                <div class="grid gap-2 sm:grid-cols-2">
                    <MobileLink href="/" label="Home" :active="currentUrl === '/'" />
                    <MobileLink href="/cars" label="Buy Car" :active="isCarsActive" />
                    <MobileLink href="/sell-your-car" label="Sell Car" :active="currentUrl === '/sell-your-car'" />
                    <MobileLink href="/verified-dealers" label="Verified Dealers" :active="isDealersActive" />
                    <MobileLink href="/contact" label="Contact" :active="currentUrl === '/contact'" />
                </div>
                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-200 pt-4 sm:grid-cols-2">
                    <template v-if="auth.customer">
                        <Link href="/customer/dashboard" class="rounded-lg border border-slate-200 px-4 py-3 text-center text-sm font-black text-slate-700 sm:col-span-2">Dashboard</Link>
                    </template>
                    <template v-else-if="auth.dealer">
                        <Link href="/dealer/dashboard" class="rounded-lg border border-slate-200 px-4 py-3 text-center text-sm font-black text-slate-700 sm:col-span-2">Dealer Panel</Link>
                    </template>
                    <template v-else>
                        <Link href="/customer/login" class="rounded-lg border border-slate-200 px-4 py-3 text-center text-sm font-black text-slate-700">Login</Link>
                        <Link href="/dealer/register" class="rounded-lg bg-orange-500 px-4 py-3 text-center text-sm font-black text-white">Dealer</Link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const showingNavigationDropdown = ref(false);

const currentUrl = computed(() => page.url || '/');
const isCarsActive = computed(() => currentUrl.value.startsWith('/cars') || currentUrl.value.startsWith('/used-') || currentUrl.value.startsWith('/car/'));
const isDealersActive = computed(() => currentUrl.value.startsWith('/verified-dealers') || currentUrl.value.startsWith('/catalog/'));
const auth = computed(() => (page.props as any).auth || {});
const NavLink = defineComponent({
    props: {
        href: { type: String, required: true },
        label: { type: String, required: true },
        active: { type: Boolean, default: false },
    },
    setup(props) {
        return () => h(Link, {
            href: props.href,
            class: [
                'inline-flex h-10 items-center rounded-lg px-3 text-sm font-bold transition',
                props.active ? 'bg-teal-50 text-teal-700' : 'text-slate-700 hover:bg-teal-50 hover:text-teal-700',
            ],
        }, () => props.label);
    },
});

const MobileLink = defineComponent({
    props: {
        href: { type: String, required: true },
        label: { type: String, required: true },
        active: { type: Boolean, default: false },
    },
    setup(props) {
        return () => h(Link, {
            href: props.href,
            class: [
                'block rounded-lg border px-3 py-3 text-sm font-black transition',
                props.active ? 'border-teal-100 bg-teal-50 text-teal-700' : 'border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50',
            ],
        }, () => props.label);
    },
});

</script>
