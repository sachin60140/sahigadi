<template>
    <footer class="bg-[#071226] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-3xl font-black tracking-tight leading-none mb-1">
                        SAHI<span class="text-[#E30613]">GADI</span>
                    </h3>
                    <p class="text-[0.65rem] font-bold text-gray-400 tracking-widest uppercase mb-6">
                        Verified. Trusted. Drive Ahead.
                    </p>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Verified used cars in Bihar. Buy, sell, and verify pre-owned cars with confidence at the best market prices.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><Link href="/" class="hover:text-white transition">Home</Link></li>
                        <li><Link href="/cars" class="hover:text-white transition">Buy a Car</Link></li>
                        <li><Link href="/sell-your-car" class="hover:text-white transition">Sell Your Car</Link></li>
                        <li><Link href="/verified-dealers" class="hover:text-white transition">Verified Dealers</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/contact" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="/privacy-policy" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="/terms-of-use" class="hover:text-white transition">Terms of Use</a></li>
                        <li><a href="/refund-policy" class="hover:text-white transition">Refund Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Support: info@sahigadi.com</li>
                        <li>Location: Muzaffarpur, Bihar</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-sm text-gray-500 flex flex-col md:flex-row justify-between items-center">
                <p>&copy; {{ new Date().getFullYear() }} SahiGadi. All rights reserved.</p>
                <div class="mt-4 md:mt-0 flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-white transition">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
