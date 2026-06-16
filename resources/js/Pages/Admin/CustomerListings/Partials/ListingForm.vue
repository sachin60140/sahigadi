<template>
    <form class="grid gap-5" @submit.prevent="$emit('submit')">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 lg:grid-cols-2">
                <Field label="Title" :error="form.errors.title" required>
                    <input v-model="form.title" class="admin-input" required type="text" placeholder="Honda City VX" />
                </Field>

                <Field label="Brand" :error="form.errors.brand_id">
                    <select v-model="form.brand_id" class="admin-input">
                        <option value="">Select brand</option>
                        <option v-for="brand in options.brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                    </select>
                </Field>

                <Field label="Model" :error="form.errors.model">
                    <input v-model="form.model" class="admin-input" type="text" />
                </Field>

                <Field label="Year" :error="form.errors.year">
                    <input v-model="form.year" class="admin-input" min="1900" :max="currentYear" type="number" />
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

                <Field label="Price" :error="form.errors.price">
                    <input v-model="form.price" class="admin-input" min="0" step="1" type="number" />
                </Field>

                <Field label="City" :error="form.errors.city">
                    <input v-model="form.city" class="admin-input" type="text" />
                </Field>

                <Field label="KM Driven" :error="form.errors.km_driven">
                    <input v-model="form.km_driven" class="admin-input" min="0" type="number" />
                </Field>

                <Field label="Registration Number" :error="form.errors.registration_number">
                    <input v-model="form.registration_number" class="admin-input uppercase" type="text" />
                </Field>

                <Field label="Owners" :error="form.errors.owners">
                    <input v-model="form.owners" class="admin-input" min="1" max="10" type="number" />
                </Field>

                <Field label="Status" :error="form.errors.status">
                    <select v-model="form.status" class="admin-input">
                        <option v-for="status in options.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </Field>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Owner details</p>
            <h3 class="mt-1 text-xl font-semibold text-slate-950">Customer contact</h3>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <Field label="Owner Phone" :error="form.errors.owner_phone" required>
                    <input v-model="form.owner_phone" class="admin-input" required type="text" />
                </Field>

                <Field label="Owner Name" :error="form.errors.owner_name">
                    <input v-model="form.owner_name" class="admin-input" type="text" />
                </Field>

                <Field label="WhatsApp Number" :error="form.errors.whatsapp_number">
                    <input v-model="form.whatsapp_number" class="admin-input" type="text" />
                </Field>

                <Field label="Images" :error="imageError">
                    <input class="admin-file" multiple accept="image/*" type="file" @change="selectImages" />
                    <p class="mt-2 text-xs font-bold text-slate-500">First image is treated as featured image. JPG, PNG, GIF up to 2 MB each.</p>
                </Field>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link :href="cancelUrl" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </Link>
            <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

type Option = { value: string; label: string };
type BrandOption = { id: number; name: string };

const props = defineProps<{
    form: any;
    options: {
        brands: BrandOption[];
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

const Field = defineComponent({
    props: {
        label: { type: String, required: true },
        error: { type: String, default: '' },
        required: { type: Boolean, default: false },
    },
    setup(fieldProps, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, [
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
.admin-input,
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
.admin-input {
    min-height: 48px;
}
.admin-input:focus,
.admin-file:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
