<template>
    <Head :title="`Edit ${car.title}`" />

    <AdminLayout :title="`Edit ${car.title}`" eyebrow="Inventory operations">
        <section class="mb-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Edit listing</p>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-semibold text-slate-950">{{ car.title }}</h2>
                        <StatusBadge :status="car.status" />
                    </div>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Update listing details, upload more photos and manage the primary image.
                    </p>
                </div>
                <Link :href="actions.back" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Back
                </Link>
            </div>
        </section>

        <CarForm :form="form" :options="options" :cancel-url="actions.back" submit-label="Update Car" @submit="submit" />

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Image manager</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-950">Current images</h3>
                </div>
                <span class="inline-flex w-fit rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ car.images.length }} images
                </span>
            </div>

            <div v-if="car.images.length" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="image in car.images" :key="image.id" class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <div class="relative aspect-[4/3] bg-slate-100">
                        <img :src="image.url" alt="" class="h-full w-full object-cover" />
                        <span v-if="image.is_primary" class="absolute left-3 top-3 rounded-md bg-teal-700 px-2.5 py-1 text-xs font-semibold text-white">Primary</span>
                    </div>
                    <div class="grid gap-2 p-3 sm:grid-cols-2">
                        <button
                            type="button"
                            class="rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-700 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="image.is_primary"
                            @click="setPrimary(image)"
                        >
                            Primary
                        </button>
                        <button type="button" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700" @click="deleteImage(image)">
                            Delete
                        </button>
                    </div>
                </article>
            </div>
            <p v-else class="mt-5 rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm font-bold text-slate-500">
                No images uploaded yet.
            </p>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { defineComponent, h } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CarForm from './Partials/CarForm.vue';

type CarImage = { id: number; url: string; is_primary: boolean; actions: Record<string, string> };
type Car = {
    title: string;
    dealer_id: number | string;
    brand_id?: number | string | null;
    model?: string | null;
    year?: number | string | null;
    fuel_type?: string | null;
    transmission?: string | null;
    km_driven?: number | string | null;
    price?: number | string | null;
    city?: string | null;
    latitude?: number | string | null;
    longitude?: number | string | null;
    registration_number?: string | null;
    owners?: number | string | null;
    status: string;
    description?: string | null;
    images: CarImage[];
};

const props = defineProps<{
    car: Car;
    options: Record<string, any>;
    actions: { update: string; back: string; index: string };
}>();

const form = useForm({
    dealer_id: props.car.dealer_id || '',
    status: props.car.status || 'pending',
    title: props.car.title || '',
    brand_id: props.car.brand_id || '',
    model: props.car.model || '',
    year: props.car.year || '',
    fuel_type: props.car.fuel_type || '',
    transmission: props.car.transmission || '',
    km_driven: props.car.km_driven || '',
    price: props.car.price || '',
    city: props.car.city || '',
    latitude: props.car.latitude || '',
    longitude: props.car.longitude || '',
    registration_number: props.car.registration_number || '',
    owners: props.car.owners || 1,
    description: props.car.description || '',
    images: [] as File[],
});

const submit = () => {
    form
        .transform((data) => ({ ...data, _method: 'PUT' }))
        .post(props.actions.update, {
            forceFormData: true,
            preserveScroll: true,
        });
};

const setPrimary = (image: CarImage) => {
    router.post(image.actions.primary, {}, { preserveScroll: true });
};

const deleteImage = (image: CarImage) => {
    if (window.confirm('Delete this image?')) {
        router.delete(image.actions.delete, { preserveScroll: true });
    }
};

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex w-fit rounded-md px-2.5 py-1 text-xs font-semibold capitalize',
                badgeProps.status === 'approved'
                    ? 'bg-teal-50 text-teal-700 ring-1 ring-teal-100'
                    : badgeProps.status === 'pending'
                        ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'
                        : 'bg-red-50 text-red-700 ring-1 ring-red-100',
            ],
        }, badgeProps.status);
    },
});
</script>
