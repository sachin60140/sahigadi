<template>
    <div class="fixed bottom-0 left-0 right-0 z-40 grid grid-cols-2 gap-2 border-t border-slate-200 bg-white p-3 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] sm:flex sm:gap-3 lg:hidden">
        
        <button v-if="!revealedContact" @click="$emit('request-contact')" class="flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-2 py-3.5 text-sm font-black text-white transition hover:bg-orange-600 sm:flex-1 sm:text-base">
            View Contact
        </button>
        
        <div v-else class="col-span-2 grid gap-2 sm:flex sm:flex-1">
            <a :href="'tel:' + revealedContact" class="flex items-center justify-center rounded-lg bg-slate-950 px-2 py-3.5 text-sm font-black text-white transition hover:bg-teal-700 sm:flex-1 sm:text-base">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                Call
            </a>
            <a :href="'https://wa.me/91' + revealedContact + '?text=Hi'" target="_blank" class="flex items-center justify-center rounded-lg bg-[#25D366] px-2 py-3.5 text-sm font-black text-white transition hover:bg-[#1DA851] sm:flex-1 sm:text-base">
                WhatsApp
            </a>
        </div>
        
        <button class="rounded-lg border border-slate-300 bg-white px-2 py-3.5 text-center text-sm font-black text-slate-700 transition hover:bg-slate-50 sm:flex-1 sm:text-base">
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
