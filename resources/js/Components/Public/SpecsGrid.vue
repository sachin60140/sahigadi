<template>
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:p-6">
        <div class="mb-5">
            <p class="text-sm font-black uppercase tracking-wide text-teal-700">Vehicle details</p>
            <h2 class="mt-1 text-xl font-black text-slate-950 md:text-2xl">Key specifications</h2>
        </div>
        
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Make Year
                </span>
                <span class="text-lg font-black text-slate-950">{{ year || 'N/A' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    KM Driven
                </span>
                <span class="text-lg font-black text-slate-950">{{ kmDriven ? kmDriven.toLocaleString('en-IN') + ' km' : '0 km' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Fuel Type
                </span>
                <span class="text-lg font-black text-slate-950">{{ fuelType || 'N/A' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Transmission
                </span>
                <span class="text-lg font-black text-slate-950">{{ transmission || 'N/A' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Ownership
                </span>
                <span class="text-lg font-black text-slate-950">{{ ownership || 'N/A' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Reg Number
                </span>
                <span class="text-lg font-black text-slate-950">{{ registrationNumberMasked || 'N/A' }}</span>
            </div>
            
            <div class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Insurance
                </span>
                <span class="text-lg font-black text-slate-950">{{ insuranceStatus || 'N/A' }}</span>
            </div>
            
            <div v-if="color" class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    Color
                </span>
                <span class="text-lg font-black text-slate-950">{{ color }}</span>
            </div>
            
            <div v-if="engine" class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Engine
                </span>
                <span class="text-lg font-black text-slate-950">{{ engine }} cc</span>
            </div>
            
            <div v-if="bodyType" class="flex min-h-[92px] flex-col rounded-lg border border-slate-100 bg-[#f7fbff] p-3">
                <span class="mb-1 flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Body Type
                </span>
                <span class="text-lg font-black text-slate-950">{{ bodyType }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    year: string | number | null;
    kmDriven: number | null;
    fuelType: string | null;
    transmission: string | null;
    ownership: string | null;
    registrationNumberMasked: string | null;
    insuranceStatus: string | null;
    color: string | null;
    engine: string | number | null;
    bodyType: string | null;
}>();
</script>

