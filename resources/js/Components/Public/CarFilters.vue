<template>
    <div class="space-y-6 bg-white p-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Filters</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Refine cars by your buying intent</p>
            </div>
            <button type="button" class="rounded-md px-2 py-1 text-sm font-semibold text-orange-600 transition hover:bg-orange-50 hover:text-orange-700" @click="$emit('clear')">
                Clear
            </button>
        </div>

        <FilterField label="Search">
            <input
                v-model="localFilters.keyword"
                type="search"
                placeholder="Model, variant, keyword..."
                class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100"
                @input="updateFilters"
            />
        </FilterField>

        <FilterField label="City">
            <select v-model="localFilters.city" class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100" @change="updateFilters">
                <option value="">All Cities</option>
                <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
            </select>
        </FilterField>

        <FilterField label="Brand">
            <select v-model="localFilters.brand" class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100" @change="updateFilters">
                <option value="">All Brands</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
            </select>
        </FilterField>

        <FilterField label="Max Budget">
            <select v-model="localFilters.max_price" class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100" @change="updateFilters">
                <option value="">Any Budget</option>
                <option v-for="opt in budgetOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
        </FilterField>

        <FilterField label="Fuel Type">
            <div class="grid grid-cols-2 gap-2">
                <FilterRadio label="Any" value="" v-model="localFilters.fuel_type" @change="updateFilters" />
                <FilterRadio v-for="fuel in fuelTypes" :key="fuel" :label="fuel" :value="normalizeOption(fuel)" v-model="localFilters.fuel_type" @change="updateFilters" />
            </div>
        </FilterField>

        <FilterField label="Transmission">
            <div class="grid grid-cols-2 gap-2">
                <FilterRadio label="Any" value="" v-model="localFilters.transmission" @change="updateFilters" />
                <FilterRadio v-for="trans in transmissions" :key="trans" :label="trans" :value="normalizeOption(trans)" v-model="localFilters.transmission" @change="updateFilters" />
            </div>
        </FilterField>

        <div class="border-t border-slate-100 pt-4">
            <button type="button" class="flex h-12 w-full items-center justify-center rounded-lg bg-slate-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-teal-700" @click="updateFilters">
                Apply Filters
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, h, ref, watch } from 'vue';

const props = defineProps<{
    filters: any;
    cities: string[];
    brands: any[];
    budgetOptions: any[];
    fuelTypes: string[];
    transmissions: string[];
}>();

const emit = defineEmits(['update', 'clear']);

const localFilters = ref({
    keyword: props.filters.keyword || '',
    city: props.filters.city || '',
    brand: props.filters.brand || '',
    max_price: props.filters.max_price || '',
    fuel_type: props.filters.fuel_type || '',
    transmission: props.filters.transmission || '',
    sort: props.filters.sort || '',
});

watch(() => props.filters, (newFilters) => {
    localFilters.value = {
        keyword: newFilters.keyword || '',
        city: newFilters.city || '',
        brand: newFilters.brand || '',
        max_price: newFilters.max_price || '',
        fuel_type: newFilters.fuel_type || '',
        transmission: newFilters.transmission || '',
        sort: newFilters.sort || '',
    };
}, { deep: true });

const updateFilters = () => {
    emit('update', localFilters.value);
};

const normalizeOption = (value: string) => value.toLowerCase();

const FilterField = defineComponent({
    props: {
        label: { type: String, required: true },
    },
    setup(props, { slots }) {
        return () => h('div', { class: 'space-y-2' }, [
            h('label', { class: 'text-sm font-semibold text-slate-800' }, props.label),
            slots.default?.(),
        ]);
    },
});

const FilterRadio = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        modelValue: { type: String, default: '' },
    },
    emits: ['update:modelValue', 'change'],
    setup(props, { emit }) {
        return () => h('label', {
            class: [
                'flex min-h-10 cursor-pointer items-center justify-center rounded-lg border px-3 py-2 text-center text-xs font-semibold transition',
                props.modelValue === props.value ? 'border-teal-600 bg-teal-50 text-teal-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50',
            ],
        }, [
            h('input', {
                type: 'radio',
                class: 'sr-only',
                value: props.value,
                checked: props.modelValue === props.value,
                onChange: () => {
                    emit('update:modelValue', props.value);
                    emit('change');
                },
            }),
            props.label,
        ]);
    },
});
</script>
