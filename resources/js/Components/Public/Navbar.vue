<template>
    <nav class="bg-[#071226] border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[72px]">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex flex-col justify-center">
                        <Link href="/" class="text-2xl font-black tracking-tight leading-none">
                            <span class="text-white">SAHI</span><span class="text-[#E30613]">GADI</span>
                        </Link>
                        <span class="text-[0.55rem] font-bold text-gray-400 tracking-wider mt-0.5 uppercase">
                            Verified. Trusted. Drive Ahead.
                        </span>
                    </div>

                    <!-- Center Navigation Links -->
                    <div class="hidden space-x-8 sm:ml-12 lg:ml-16 md:flex">
                        <Link href="/" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold transition-colors duration-200"
                              :class="$page.url === '/' ? 'border-[#E30613] text-white' : 'border-transparent text-gray-300 hover:text-white'">
                            Home
                        </Link>
                        <Link href="/cars" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold transition-colors duration-200"
                              :class="$page.url.startsWith('/cars') || $page.url.startsWith('/used-') ? 'border-[#E30613] text-white' : 'border-transparent text-gray-300 hover:text-white'">
                            Buy a Car
                        </Link>
                        <Link href="/sell-your-car" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold transition-colors duration-200"
                              :class="$page.url === '/sell-your-car' ? 'border-[#E30613] text-white' : 'border-transparent text-gray-300 hover:text-white'">
                            Sell Your Car
                        </Link>
                        <Link href="/verified-dealers" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold transition-colors duration-200"
                              :class="$page.url.startsWith('/verified-dealers') || $page.url.startsWith('/dealer/') && !$page.url.includes('register') ? 'border-[#E30613] text-white' : 'border-transparent text-gray-300 hover:text-white'">
                            Dealers
                        </Link>
                        <div class="relative inline-flex items-center cursor-pointer group px-1 pt-1 border-b-2 border-transparent text-sm font-semibold text-gray-300 hover:text-white transition-colors duration-200">
                            Services
                            <svg class="ml-1 w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden md:flex items-center space-x-6">
                    <template v-if="$page.props.auth?.customer">
                        <Link href="/customer/dashboard" class="text-gray-300 hover:text-white text-sm font-semibold flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Dashboard
                        </Link>
                    </template>
                    <template v-else-if="$page.props.auth?.dealer">
                        <Link href="/dealer/dashboard" class="text-gray-300 hover:text-white text-sm font-semibold flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Dealer Panel
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/customer/login" class="text-gray-300 hover:text-white text-sm font-semibold flex items-center transition-colors">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Login
                        </Link>
                        <Link href="/dealer/register" class="bg-[#E30613] hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Become a Dealer
                        </Link>
                    </template>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center md:hidden">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none focus:bg-gray-800 focus:text-white transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="md:hidden absolute w-full bg-[#0B1F3A] border-b border-gray-800 shadow-xl">
            <div class="pt-2 pb-3 space-y-1">
                <Link href="/" class="block pl-3 pr-4 py-3 border-l-4 text-base font-semibold transition duration-150 ease-in-out"
                      :class="$page.url === '/' ? 'border-[#E30613] text-white bg-gray-800/50' : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-800/30'">
                    Home
                </Link>
                <Link href="/cars" class="block pl-3 pr-4 py-3 border-l-4 text-base font-semibold transition duration-150 ease-in-out"
                      :class="$page.url.startsWith('/cars') || $page.url.startsWith('/used-') ? 'border-[#E30613] text-white bg-gray-800/50' : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-800/30'">
                    Buy a Car
                </Link>
                <Link href="/sell-your-car" class="block pl-3 pr-4 py-3 border-l-4 text-base font-semibold transition duration-150 ease-in-out"
                      :class="$page.url === '/sell-your-car' ? 'border-[#E30613] text-white bg-gray-800/50' : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-800/30'">
                    Sell Your Car
                </Link>
                <Link href="/verified-dealers" class="block pl-3 pr-4 py-3 border-l-4 text-base font-semibold transition duration-150 ease-in-out"
                      :class="$page.url.startsWith('/verified-dealers') ? 'border-[#E30613] text-white bg-gray-800/50' : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-800/30'">
                    Dealers
                </Link>
            </div>
            <div class="pt-4 pb-4 border-t border-gray-700">
                <template v-if="$page.props.auth?.customer">
                    <Link href="/customer/dashboard" class="block px-4 py-2 text-base font-semibold text-gray-300 hover:text-white hover:bg-gray-800/50">Dashboard</Link>
                </template>
                <template v-else-if="$page.props.auth?.dealer">
                    <Link href="/dealer/dashboard" class="block px-4 py-2 text-base font-semibold text-gray-300 hover:text-white hover:bg-gray-800/50">Dealer Panel</Link>
                </template>
                <template v-else>
                    <Link href="/customer/login" class="block px-4 py-2 text-base font-semibold text-gray-300 hover:text-white hover:bg-gray-800/50">Login</Link>
                    <Link href="/dealer/register" class="block px-4 py-2 mt-2 text-base font-bold text-[#E30613] hover:text-red-400">Become a Dealer</Link>
                </template>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>
