<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_15px_rgb(0,0,0,0.05)] z-40 lg:hidden flex gap-3 pb-safe animate-in slide-in-from-bottom duration-300">
        
        <button v-if="!revealedContact" @click="$emit('request-contact')" class="flex-1 bg-[#E30613] hover:bg-red-700 text-white font-bold py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2">
            View Contact
        </button>
        
        <div v-else class="flex-1 flex gap-2">
            <a :href="'tel:' + revealedContact" class="flex-1 bg-[#071226] hover:bg-[#0B1F3A] text-white font-bold py-3.5 rounded-xl transition-colors flex items-center justify-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                Call
            </a>
            <a :href="'https://wa.me/91' + revealedContact + '?text=Hi'" target="_blank" class="flex-1 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold py-3.5 rounded-xl transition-colors flex items-center justify-center">
                WhatsApp
            </a>
        </div>
        
        <button class="flex-1 bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-3.5 rounded-xl transition-colors text-center">
            Send Enquiry
        </button>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    revealedContact: string | null;
}>();

defineEmits(['request-contact']);
</script>
