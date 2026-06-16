<template>
    <div v-if="hasActiveFilters" class="mb-5 flex flex-wrap items-center gap-2">
        <span class="text-sm font-bold text-slate-500">Active filters</span>

        <FilterChip v-if="filters.keyword" :label="`Search: ${filters.keyword}`" @remove="remove('keyword')" />
        <FilterChip v-if="filters.city" :label="`City: ${filters.city}`" @remove="remove('city')" />
        <FilterChip v-if="filters.brand" :label="`Brand: ${brandName}`" @remove="remove('brand')" />
        <FilterChip v-if="filters.max_price" :label="`Budget: ${formatPrice(filters.max_price)}`" @remove="remove('max_price')" />
        <FilterChip v-if="filters.fuel_type" :label="`Fuel: ${formatLabel(filters.fuel_type)}`" @remove="remove('fuel_type')" />
        <FilterChip v-if="filters.transmission" :label="`Trans: ${formatLabel(filters.transmission)}`" @remove="remove('transmission')" />

        <button type="button" class="ml-1 text-sm font-semibold text-orange-600 transition hover:text-orange-700" @click="$emit('clear')">
            Clear all
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';

const props = defineProps<{
    filters: any;
    brands: any[];
}>();

const emit = defineEmits(['remove', 'clear']);

const hasActiveFilters = computed(() => Boolean(
    props.filters.keyword ||
    props.filters.city ||
    props.filters.brand ||
    props.filters.max_price ||
    props.filters.fuel_type ||
    props.filters.transmission
));

const brandName = computed(() => {
    if (!props.filters.brand) return '';
    const brand = props.brands.find((b) => b.id == props.filters.brand);
    return brand ? brand.name : 'Unknown';
});

const formatPrice = (price: string | number) => {
    const num = Number(price);
    if (num >= 100000) {
        return `Under ₹${(num / 100000).toFixed(num % 100000 === 0 ? 0 : 1)} Lakh`;
    }
    return `Under ₹${num.toLocaleString('en-IN')}`;
};

const formatLabel = (value: string) => value ? value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() : '';

const remove = (key: string) => {
    emit('remove', key);
};

const FilterChip = defineComponent({
    props: {
        label: { type: String, required: true },
    },
    emits: ['remove'],
    setup(props, { emit }) {
        return () => h('span', {
            class: 'inline-flex items-center gap-1.5 rounded-lg border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-800',
        }, [
            props.label,
            h('button', {
                type: 'button',
                class: 'rounded text-teal-700 transition hover:text-teal-950',
                onClick: () => emit('remove'),
                'aria-label': `Remove ${props.label}`,
            }, [
                h('svg', { class: 'h-3.5 w-3.5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M6 18L18 6M6 6l12 12' }),
                ]),
            ]),
        ]);
    },
});
</script>
