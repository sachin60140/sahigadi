<template>
    <section v-if="cars && cars.length > 0" class="bg-slate-50 py-14 md:py-20">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
            <div class="mb-9 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-teal-700">Editor's picks</p>
                    <h2 class="text-3xl font-semibold tracking-normal text-slate-950 md:text-4xl">Featured Cars</h2>
                    <p class="mt-2 max-w-2xl text-base font-medium text-slate-500">Handpicked verified cars with strong value, trusted sellers, and clear pricing.</p>
                </div>
                <Link href="/cars" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                    View All Cars
                    <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </Link>
            </div>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <CarCard v-for="car in cars" :key="car.id" :car="car" />
            </div>
            
            <div class="mt-8 text-center sm:hidden">
                <Link href="/cars" class="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-6 py-3.5 font-semibold text-white shadow-md transition hover:bg-teal-700">
                    View All Featured Cars
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import CarCard from './CarCard.vue';

defineProps<{
    cars: any[];
}>();
</script>
