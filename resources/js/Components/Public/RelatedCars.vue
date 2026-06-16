<template>
    <section v-if="cars && cars.length > 0" class="mt-12">
        <div class="mb-6 border-b border-slate-200 pb-4">
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-teal-700">More options</p>
            <h2 class="text-2xl font-semibold tracking-normal text-slate-950">Similar Cars You May Like</h2>
        </div>
        
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <CarCard v-for="car in cars" :key="car.id" :car="car" />
        </div>
    </section>
</template>

<script setup lang="ts">
import CarCard from './CarCard.vue';

defineProps<{
    cars: any[];
}>();
</script>
