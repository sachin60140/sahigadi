<template>
    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5 mt-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white shadow-sm border border-gray-200 rounded-full flex items-center justify-center text-[#071226] font-bold text-xl">
                {{ profile.display_name.charAt(0).toUpperCase() }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <p class="font-bold text-gray-900 text-lg">{{ profile.display_name }}</p>
                    <span v-if="profile.is_verified" class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-semibold flex items-center" title="Verified Seller">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zM8.28 11.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.06 0l4.25-4.25a.75.75 0 00-1.06-1.06l-3.72 3.72-1.72-1.72z" clip-rule="evenodd"></path></svg>
                        Verified
                    </span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ profile.type }}
                    </span>
                    <span v-if="profile.member_since" class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Since {{ profile.member_since }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    profile: {
        display_name: string;
        type: string;
        city: string | null;
        is_verified: boolean;
        member_since: string | null;
        masked_mobile: string;
    };
}>();
</script>
