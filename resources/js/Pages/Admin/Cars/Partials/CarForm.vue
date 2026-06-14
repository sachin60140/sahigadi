<template>
    <form class="grid gap-5" @submit.prevent="$emit('submit')">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 lg:grid-cols-2">
                <Field label="Dealer" :error="form.errors.dealer_id" required>
                    <select v-model="form.dealer_id" class="admin-input" required>
                        <option value="">Select dealer</option>
                        <option v-for="dealer in options.dealers" :key="dealer.id" :value="dealer.id">{{ dealer.label }}</option>
                    </select>
                </Field>

                <Field label="Status" :error="form.errors.status" required>
                    <select v-model="form.status" class="admin-input" required>
                        <option v-for="status in options.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </Field>

                <Field label="Title" :error="form.errors.title" required>
                    <input v-model="form.title" class="admin-input" required type="text" placeholder="Mahindra Bolero B6" />
                </Field>

                <Field label="Brand" :error="form.errors.brand_id">
                    <select v-model="form.brand_id" class="admin-input">
                        <option value="">Select brand</option>
                        <option v-for="brand in options.brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                    </select>
                </Field>

                <Field label="Model" :error="form.errors.model">
                    <input v-model="form.model" class="admin-input" type="text" placeholder="Bolero" />
                </Field>

                <Field label="Year" :error="form.errors.year">
                    <input v-model="form.year" class="admin-input" :max="currentYear" min="1900" type="number" />
                </Field>

                <Field label="Fuel Type" :error="form.errors.fuel_type">
                    <select v-model="form.fuel_type" class="admin-input">
                        <option value="">Select fuel</option>
                        <option v-for="fuel in options.fuelTypes" :key="fuel.value" :value="fuel.value">{{ fuel.label }}</option>
                    </select>
                </Field>

                <Field label="Transmission" :error="form.errors.transmission">
                    <select v-model="form.transmission" class="admin-input">
                        <option value="">Select transmission</option>
                        <option v-for="item in options.transmissions" :key="item.value" :value="item.value">{{ item.label }}</option>
                    </select>
                </Field>

                <Field label="Kilometers Driven" :error="form.errors.km_driven">
                    <input v-model="form.km_driven" class="admin-input" min="0" type="number" />
                </Field>

                <Field label="Price" :error="form.errors.price">
                    <input v-model="form.price" class="admin-input" min="0" step="1" type="number" />
                </Field>

                <Field label="City" :error="form.errors.city">
                    <input v-model="form.city" class="admin-input" type="text" placeholder="Patna" />
                </Field>

                <Field label="Registration Number" :error="form.errors.registration_number">
                    <input v-model="form.registration_number" class="admin-input uppercase" type="text" placeholder="BR01AB1234" />
                </Field>

                <Field label="Owners" :error="form.errors.owners">
                    <select v-model="form.owners" class="admin-input">
                        <option v-for="owner in [1, 2, 3, 4, 5]" :key="owner" :value="owner">{{ owner }}</option>
                    </select>
                </Field>

                <div class="grid gap-4 sm:grid-cols-2">
                    <Field label="Latitude" :error="form.errors.latitude">
                        <input v-model="form.latitude" class="admin-input" type="text" />
                    </Field>
                    <Field label="Longitude" :error="form.errors.longitude">
                        <input v-model="form.longitude" class="admin-input" type="text" />
                    </Field>
                </div>

                <div class="lg:col-span-2">
                    <button type="button" class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-700" @click="useCurrentLocation">
                        Use Current Location
                    </button>
                </div>

                <div class="lg:col-span-2">
                    <Field label="Description" :error="form.errors.description">
                        <textarea v-model="form.description" class="admin-input min-h-32" placeholder="Describe condition, service record and selling notes."></textarea>
                    </Field>
                </div>

                <div class="lg:col-span-2">
                    <Field label="Images" :error="imageError">
                        <input class="admin-file" multiple accept="image/*" type="file" @change="selectImages" />
                        <p class="mt-2 text-xs font-bold text-slate-500">First uploaded image becomes primary for new cars. JPG, PNG, GIF up to 2 MB each.</p>
                    </Field>
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link :href="cancelUrl" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                Cancel
            </Link>
            <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-700" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

type Option = { value: string; label: string };
type IdOption = { id: number; name?: string; label?: string };

const props = defineProps<{
    form: any;
    options: {
        brands: IdOption[];
        dealers: IdOption[];
        fuelTypes: Option[];
        transmissions: Option[];
        statuses: Option[];
    };
    submitLabel: string;
    cancelUrl: string;
}>();

defineEmits<{ submit: [] }>();

const currentYear = new Date().getFullYear();
const imageError = computed(() => props.form.errors.images || props.form.errors['images.0'] || props.form.errors['images.*'] || '');

const selectImages = (event: Event) => {
    props.form.images = Array.from((event.target as HTMLInputElement).files || []);
};

const useCurrentLocation = () => {
    if (!navigator.geolocation) {
        return;
    }

    navigator.geolocation.getCurrentPosition((position) => {
        props.form.latitude = String(position.coords.latitude);
        props.form.longitude = String(position.coords.longitude);
    });
};

const Field = defineComponent({
    props: {
        label: { type: String, required: true },
        error: { type: String, default: '' },
        required: { type: Boolean, default: false },
    },
    setup(fieldProps, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, [
                fieldProps.label,
                fieldProps.required ? h('span', { class: 'text-red-600' }, ' *') : null,
            ]),
            slots.default?.(),
            fieldProps.error ? h('span', { class: 'mt-2 block text-xs font-bold text-red-700' }, fieldProps.error) : null,
        ]);
    },
});
</script>

<style scoped>
.admin-input {
    min-height: 48px;
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 12px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(30 41 59);
    outline: none;
}
.admin-input:focus,
.admin-file:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
.admin-file {
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
    padding: 12px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(30 41 59);
    outline: none;
}
</style>
