<template>
    <div v-if="cars && cars.length > 0" class="mt-16">
        <h2 class="text-2xl font-bold text-[#071226] mb-6 border-b border-gray-200 pb-4">Similar Cars You May Like</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <CarCard v-for="car in cars" :key="car.id" :car="car" />
        </div>
    </div>
</template>

<script setup lang="ts">
import CarCard from './CarCard.vue';

defineProps<{
    cars: any[];
}>();
</script>
