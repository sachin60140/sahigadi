<template>
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#071226] mb-8">Browse by Budget & Body Type</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                <Link href="/cars?max_price=300000" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <span class="text-xl font-bold text-gray-800 group-hover:text-[#1E40AF]">Under</span>
                    <span class="text-lg font-bold text-[#E30613] mt-1">₹3 Lakh</span>
                </Link>
                
                <Link href="/cars?max_price=500000" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <span class="text-xl font-bold text-gray-800 group-hover:text-[#1E40AF]">Under</span>
                    <span class="text-lg font-bold text-[#E30613] mt-1">₹5 Lakh</span>
                </Link>
                
                <Link href="/cars?max_price=700000" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <span class="text-xl font-bold text-gray-800 group-hover:text-[#1E40AF]">Under</span>
                    <span class="text-lg font-bold text-[#E30613] mt-1">₹7 Lakh</span>
                </Link>
                
                <Link href="/cars?max_price=1000000" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <span class="text-xl font-bold text-gray-800 group-hover:text-[#1E40AF]">Under</span>
                    <span class="text-lg font-bold text-[#E30613] mt-1">₹10 Lakh</span>
                </Link>
                
                <Link href="/cars?body_type=SUV" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-[#1E40AF] mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8a2 2 0 012 2v2M4 11h16a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4a2 2 0 012-2zM4 11V9a2 2 0 012-2h1M6 19v2a1 1 0 001 1h2a1 1 0 001-1v-2M14 19v2a1 1 0 001 1h2a1 1 0 001-1v-2M8 15a2 2 0 100-4 2 2 0 000 4zM16 15a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#1E40AF]">SUV Cars</span>
                </Link>

                <Link href="/cars?transmission=Automatic" class="bg-gray-50 hover:bg-white border border-gray-100 hover:border-[#1E40AF] rounded-xl p-4 flex flex-col items-center justify-center transition-all hover:shadow-md group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-[#1E40AF] mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#1E40AF]">Automatic Cars</span>
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
