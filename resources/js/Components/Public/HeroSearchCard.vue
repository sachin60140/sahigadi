<template>
    <div class="relative z-10 mx-auto w-full max-w-md rounded-lg border border-white/70 bg-white p-5 shadow-2xl shadow-slate-950/20 sm:p-6 md:p-7 xl:ml-auto xl:mr-0">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-950">Find your car</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">Search verified cars across Bihar</p>
            </div>
            <span class="rounded-lg bg-teal-50 px-3 py-2 text-xs font-black text-teal-700">Verified</span>
        </div>

        <form class="space-y-4" @submit.prevent="submitSearch">
            <div>
                <label class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-600">Search</label>
                <input
                    v-model="form.keyword"
                    type="search"
                    placeholder="Swift, Creta, Baleno..."
                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-800 shadow-sm transition hover:bg-white focus:border-teal-700 focus:ring-teal-700"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FieldSelect v-model="form.city" label="City">
                    <option value="">All Cities</option>
                    <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                </FieldSelect>

                <FieldSelect v-model="form.brand" label="Brand">
                    <option value="">All Brands</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                </FieldSelect>

                <FieldSelect v-model="form.max_price" label="Budget">
                    <option value="">Any Budget</option>
                    <option v-for="option in budgetOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </FieldSelect>

                <FieldSelect v-model="form.fuel_type" label="Fuel Type">
                    <option value="">Any Fuel</option>
                    <option v-for="fuel in fuelTypes" :key="fuel" :value="normalizeOption(fuel)">{{ fuel }}</option>
                </FieldSelect>
            </div>

            <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-orange-500 px-4 py-3.5 font-black text-white shadow-md transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Search Cars
            </button>

            <div class="flex items-center justify-center border-t border-slate-100 pt-3">
                <svg class="mr-1.5 h-4 w-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-xs font-semibold text-slate-500">Safe listings with verified seller contact</span>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps<{
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
}>();

const form = useForm({
    keyword: '',
    city: '',
    brand: '',
    max_price: '',
    fuel_type: '',
});

const submitSearch = () => {
    form.get('/cars', {
        preserveState: true,
    });
};

const normalizeOption = (value: string) => value.toLowerCase();

const FieldSelect = defineComponent({
    props: {
        label: { type: String, required: true },
        modelValue: { type: [String, Number], default: '' },
    },
    emits: ['update:modelValue'],
    setup(props, { slots, emit }) {
        return () => h('div', [
            h('label', { class: 'mb-1 block text-xs font-black uppercase tracking-wide text-slate-600' }, props.label),
            h('div', { class: 'relative' }, [
                h('select', {
                    value: props.modelValue,
                    class: 'w-full cursor-pointer appearance-none rounded-lg border border-slate-300 bg-slate-50 py-3 pl-3 pr-10 text-sm font-medium text-slate-800 shadow-sm transition hover:bg-white focus:border-teal-700 focus:ring-teal-700',
                    onChange: (event: Event) => emit('update:modelValue', (event.target as HTMLSelectElement).value),
                }, slots.default?.()),
                h('div', { class: 'pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400' }, [
                    h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 9l-7 7-7-7' }),
                    ]),
                ]),
            ]),
        ]);
    },
});
</script>
