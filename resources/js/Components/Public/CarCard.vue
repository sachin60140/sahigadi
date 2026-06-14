<template>
    <Link
        :href="`/car/${car.slug}`"
        :aria-label="`View details for ${title}`"
        class="group flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-xl"
    >
        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
            <img
                :src="imageUrl"
                :alt="`${title} used car in ${car.city || 'Bihar'}`"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                loading="lazy"
                @error="handleImageError"
            />

            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                <span v-if="isVerified" class="rounded-md bg-teal-700 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-white shadow-sm">
                    Verified
                </span>
                <span v-if="car.is_featured" class="rounded-md bg-orange-50 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-orange-700 ring-1 ring-orange-200">
                    Featured
                </span>
            </div>

            <span class="absolute right-3 top-3 rounded-md bg-slate-950/85 px-2.5 py-1 text-xs font-black text-white">
                {{ car.year || 'N/A' }}
            </span>
        </div>

        <div class="flex flex-1 flex-col p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="line-clamp-2 text-base font-black leading-snug text-slate-950 transition group-hover:text-teal-700">
                        {{ title }}
                    </h3>
                    <p class="mt-1 line-clamp-1 text-sm font-semibold text-slate-500">
                        {{ car.variant || car.model || 'Standard Variant' }}
                    </p>
                </div>
            </div>

            <div class="mt-3 flex items-center gap-1.5 text-sm font-semibold text-slate-500">
                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ car.city || 'Bihar' }}</span>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-bold text-slate-600">
                <SpecPill :label="formattedKm" />
                <SpecPill :label="formatLabel(car.fuel_type || 'Fuel')" />
                <SpecPill :label="formatLabel(car.transmission || 'Manual')" />
            </div>

            <div class="mt-5 flex flex-1 flex-col items-start gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Price</p>
                    <p class="mt-1 text-xl font-black text-slate-950">₹{{ formattedPrice }}</p>
                </div>
                <span class="inline-flex rounded-lg bg-slate-950 px-3 py-2 text-sm font-black text-white transition group-hover:bg-teal-700">
                    Details
                </span>
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    car: any;
}>();

const title = computed(() => {
    const brandName = props.car.brand?.name || '';
    const model = props.car.model || '';
    return props.car.title || `${brandName} ${model}`.trim() || 'Used Car';
});

const fallbackImage = '/images/og-image.png';
const imageUrl = computed(() => props.car.image_url || fallbackImage);

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (!target.src.endsWith(fallbackImage)) {
        target.src = fallbackImage;
    }
};

const isVerified = computed(() => Boolean(props.car.is_verified));

const formattedPrice = computed(() => new Intl.NumberFormat('en-IN', {
    maximumFractionDigits: 0,
}).format(props.car.price || 0));

const formattedKm = computed(() => {
    const km = Number(props.car.km_driven || 0);
    return km > 0 ? `${new Intl.NumberFormat('en-IN').format(km)} km` : 'KMs N/A';
});

const formatLabel = (value: string) => value ? value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() : 'N/A';

const SpecPill = defineComponent({
    props: {
        label: { type: String, required: true },
    },
    setup(props) {
        return () => h('span', {
            class: 'truncate rounded-md bg-slate-50 px-2 py-2 text-center ring-1 ring-slate-100',
            title: props.label,
        }, props.label);
    },
});
</script>
