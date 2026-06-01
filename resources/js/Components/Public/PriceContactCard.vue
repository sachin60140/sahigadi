<template>
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 p-6 xl:p-8 sticky top-24">
        <!-- Basic Header Info -->
        <div class="mb-4">
            <div class="flex justify-between items-start">
                <h1 class="text-2xl xl:text-3xl font-extrabold text-[#071226] leading-tight mb-2">{{ title }}</h1>
            </div>
            <p class="text-gray-500 font-medium">{{ variant || model }}</p>
        </div>
        
        <div class="flex items-center gap-2 mb-6 text-gray-600 text-sm font-medium">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ city }}<span v-if="state">, {{ state }}</span>
        </div>

        <div class="py-6 border-t border-b border-gray-100 mb-6 bg-gray-50 -mx-6 xl:-mx-8 px-6 xl:px-8">
            <p class="text-sm text-gray-500 mb-1 font-semibold uppercase tracking-wider">Asking Price</p>
            <p class="text-4xl font-black text-[#071226]">₹{{ formattedPrice }}</p>
            <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#E30613]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Contact seller for best price
            </p>
        </div>

        <div class="space-y-4">
            <button 
                v-if="!revealedContact"
                @click="$emit('request-contact')" 
                class="w-full bg-[#E30613] hover:bg-red-700 text-white font-bold py-4 rounded-xl transition-all shadow-md flex items-center justify-center text-lg gap-2"
            >
                View Contact
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </button>

            <!-- Revealed Contact Options -->
            <div v-else class="space-y-3 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center justify-center gap-3 font-bold text-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    {{ revealedContact }}
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <a :href="'tel:' + revealedContact" class="bg-[#071226] hover:bg-[#0B1F3A] text-white font-bold py-3 rounded-xl transition-all shadow text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Call Now
                    </a>
                    <a :href="'https://wa.me/91' + revealedContact + '?text=Hi, I am interested in your ' + title" target="_blank" class="bg-[#25D366] hover:bg-[#1DA851] text-white font-bold py-3 rounded-xl transition-all shadow text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
            
            <button class="w-full bg-white border border-gray-300 hover:border-gray-400 hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-xl transition-all shadow-sm">
                Send Enquiry
            </button>
        </div>
        
        <p class="text-xs text-center text-gray-400 mt-6 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            OTP verification required to protect buyer and seller privacy
        </p>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    title: string;
    model: string;
    variant: string;
    price: number;
    city: string;
    state: string | null;
    revealedContact: string | null;
}>();

defineEmits(['request-contact']);

const formattedPrice = computed(() => {
    return props.price?.toLocaleString('en-IN') || '0';
});
</script>
