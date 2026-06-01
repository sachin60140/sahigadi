<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="relative aspect-[4/3] sm:aspect-[16/9] lg:aspect-[4/3] bg-gray-100">
            <img 
                :src="activeImage" 
                :alt="title" 
                class="w-full h-full object-cover transition-opacity duration-300"
                @error="handleImageError"
            />
            
            <div v-if="isVerified" class="absolute top-4 right-4 bg-green-600/95 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-sm font-medium flex items-center shadow-md">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verified
            </div>

            <!-- Share and Favorite Buttons -->
            <div class="absolute top-4 left-4 flex gap-2">
                <button @click="shareLink" class="bg-white/90 backdrop-blur text-gray-700 hover:text-[#071226] p-2 rounded-full shadow transition-colors" aria-label="Share">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
                <button @click="toggleFavorite" class="bg-white/90 backdrop-blur text-gray-700 hover:text-red-500 p-2 rounded-full shadow transition-colors" aria-label="Favorite">
                    <svg class="w-5 h-5" :fill="isFavorited ? 'currentColor' : 'none'" :class="isFavorited ? 'text-red-500' : ''" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </button>
            </div>
        </div>
        
        <div v-if="images && images.length > 1" class="flex overflow-x-auto gap-3 p-4 no-scrollbar bg-white">
            <button v-for="(img, idx) in images" :key="idx" 
                    @click="activeImage = img"
                    class="shrink-0 w-24 h-18 rounded-lg overflow-hidden border-2 transition-all focus:outline-none"
                    :class="activeImage === img ? 'border-[#071226]' : 'border-transparent opacity-70 hover:opacity-100 hover:border-gray-300'">
                <img :src="img" :alt="title + ' thumbnail ' + (idx+1)" loading="lazy" @error="handleThumbnailError" class="w-full h-full object-cover" />
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';

const props = defineProps<{
    images: string[];
    mainImage: string;
    title: string;
    isVerified: boolean;
}>();

const activeImage = ref(props.mainImage || '/images/car-placeholder.webp');
const isFavorited = ref(false);

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
