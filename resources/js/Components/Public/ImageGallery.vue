<template>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="relative aspect-[4/3] bg-slate-100 sm:aspect-[16/9] lg:aspect-[4/3]">
            <img 
                :src="activeImage" 
                :alt="`${title} used car photos`" 
                class="h-full w-full object-cover transition-opacity duration-300"
                @error="handleImageError"
            />
            
            <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 bg-gradient-to-t from-slate-950/75 to-transparent p-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-white/80">Gallery</p>
                    <p class="mt-1 text-sm font-bold text-white">{{ activeIndex + 1 }} of {{ photoCount }} photos</p>
                </div>
                <div v-if="isVerified" class="flex items-center rounded-lg bg-teal-700/95 px-3 py-1.5 text-sm font-black text-white shadow-md backdrop-blur-sm">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Verified
                </div>
            </div>

            <div v-if="isVerified" class="sr-only">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Verified
            </div>

            <div class="absolute left-4 top-4 flex gap-2">
                <button @click="shareLink" class="rounded-lg bg-white/95 p-2 text-slate-700 shadow transition hover:text-teal-700" aria-label="Share this car">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                </button>
                <button @click="toggleFavorite" class="rounded-lg bg-white/95 p-2 text-slate-700 shadow transition hover:text-orange-500" aria-label="Save this car">
                    <svg class="h-5 w-5" :fill="isFavorited ? 'currentColor' : 'none'" :class="isFavorited ? 'text-orange-500' : ''" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </button>
            </div>
        </div>
        
        <div v-if="galleryImages.length > 1" class="no-scrollbar flex gap-3 overflow-x-auto border-t border-slate-100 bg-white p-4">
            <button v-for="(img, idx) in galleryImages" :key="idx" 
                    @click="activeImage = img"
                    class="h-[72px] w-24 shrink-0 overflow-hidden rounded-lg border-2 bg-slate-100 transition-all focus:outline-none focus:ring-4 focus:ring-teal-100"
                    :class="activeImage === img ? 'border-teal-600 opacity-100 shadow-sm' : 'border-transparent opacity-70 hover:border-slate-300 hover:opacity-100'">
                <img :src="img" :alt="title + ' thumbnail ' + (idx+1)" loading="lazy" @error="handleThumbnailError" class="h-full w-full object-cover" />
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    images: string[];
    mainImage: string;
    title: string;
    isVerified: boolean;
}>();

const activeImage = ref(props.mainImage || '/images/car-placeholder.webp');
const isFavorited = ref(false);

const galleryImages = computed(() => props.images?.length ? props.images : [props.mainImage || '/images/car-placeholder.webp']);
const photoCount = computed(() => galleryImages.value.length);
const activeIndex = computed(() => {
    const index = galleryImages.value.indexOf(activeImage.value);
    return index >= 0 ? index : 0;
});

watch(() => props.mainImage, (newImg) => {
    activeImage.value = newImg || '/images/car-placeholder.webp';
});

const handleImageError = (e: Event) => {
    (e.target as HTMLImageElement).src = '/images/car-placeholder.webp';
};

const handleThumbnailError = (e: Event) => {
    (e.target as HTMLImageElement).src = '/images/car-placeholder.webp';
};

const shareLink = async () => {
    try {
        if (navigator.share) {
            await navigator.share({
                title: props.title,
                url: window.location.href
            });
        } else {
            await navigator.clipboard.writeText(window.location.href);
            alert('Link copied to clipboard!');
        }
    } catch (err) {
        console.error('Error sharing:', err);
    }
};

const toggleFavorite = () => {
    isFavorited.value = !isFavorited.value;
};
</script>
