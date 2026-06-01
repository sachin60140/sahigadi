<template>
    <div class="text-center py-16 px-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h3 class="mt-4 text-lg font-bold text-gray-900">No cars found</h3>
        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">We couldn't find any cars matching your current filters. Try changing your city, budget, or brand filter to see more results.</p>
        <div class="mt-6">
            <button @click="$emit('clear')" type="button" class="inline-flex items-center rounded-md bg-[#E30613] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition-colors">
                Clear all filters
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
defineEmits(['clear']);
</script>
