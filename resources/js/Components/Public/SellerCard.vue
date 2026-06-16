<template>
    <div v-if="profile" class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-lg font-semibold text-teal-700">
                {{ sellerInitial }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="truncate text-lg font-semibold text-slate-950">{{ profile.display_name }}</p>
                    <span v-if="profile.is_verified" class="inline-flex items-center rounded-md bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">
                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zM8.28 11.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.06 0l4.25-4.25a.75.75 0 00-1.06-1.06l-3.72 3.72-1.72-1.72z" clip-rule="evenodd" />
                        </svg>
                        Verified
                    </span>
                </div>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-2 text-sm font-semibold text-slate-500">
                    <span>{{ profile.type }}</span>
                    <span v-if="profile.city">{{ profile.city }}</span>
                    <span v-if="profile.member_since">Since {{ profile.member_since }}</span>
                </div>
                <p v-if="profile.masked_mobile" class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm font-bold text-slate-600">
                    Contact: {{ profile.masked_mobile }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    profile: {
        display_name: string;
        type: string;
        city: string | null;
        is_verified: boolean;
        member_since: string | null;
        masked_mobile: string;
    } | null;
}>();

const sellerInitial = computed(() => props.profile?.display_name?.charAt(0).toUpperCase() || 'S');
</script>
