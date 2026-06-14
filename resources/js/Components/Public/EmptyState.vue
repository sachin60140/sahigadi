<template>
    <div class="rounded-lg border border-slate-200 bg-white px-4 py-16 text-center shadow-sm">
        <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h3 class="mt-4 text-lg font-black text-slate-950">No cars found</h3>
        <p class="mx-auto mt-2 max-w-md text-sm font-medium leading-6 text-slate-500">Try changing your city, budget, or brand filter to see more verified cars.</p>
        <div class="mt-6">
            <button @click="$emit('clear')" type="button" class="inline-flex items-center rounded-lg bg-orange-500 px-6 py-3 text-sm font-black text-white shadow-sm transition hover:bg-orange-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                Clear all filters
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
defineEmits(['clear']);
</script>
