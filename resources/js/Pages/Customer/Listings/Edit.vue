<template>
    <Head title="Edit Car Listing" />
    <CustomerLayout title="Edit Listing" eyebrow="Vehicle inventory">
        <div class="mx-auto max-w-5xl">
            <Link :href="actions.dashboard" class="inline-flex items-center gap-2 text-sm font-semibold text-teal-700 hover:text-teal-900">
                <ArrowLeft class="h-4 w-4" />
                Back to dashboard
            </Link>

            <form class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" @submit.prevent="submit">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">{{ listing.unique_id }}</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-950">Vehicle details</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Changes return the listing to pending review.</p>
                </div>

                <div class="space-y-8 p-5 sm:p-6">
                    <section>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Field label="Listing title" :error="form.errors.title" class="sm:col-span-2 lg:col-span-3">
                                <input v-model="form.title" class="customer-input" required />
                            </Field>
                            <Field label="Brand" :error="form.errors.brand_id">
                                <select v-model="form.brand_id" class="customer-input">
                                    <option value="">Select brand</option>
                                    <option v-for="brand in options.brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                                </select>
                            </Field>
                            <Field label="Model" :error="form.errors.model"><input v-model="form.model" class="customer-input" /></Field>
                            <Field label="Year" :error="form.errors.year"><input v-model="form.year" class="customer-input" type="number" min="1900" :max="currentYear" /></Field>
                            <Field label="Fuel type" :error="form.errors.fuel_type">
                                <select v-model="form.fuel_type" class="customer-input">
                                    <option value="">Select fuel</option>
                                    <option v-for="item in options.fuelTypes" :key="item.value" :value="item.value">{{ item.label }}</option>
                                </select>
                            </Field>
                            <Field label="Transmission" :error="form.errors.transmission">
                                <select v-model="form.transmission" class="customer-input">
                                    <option value="">Select transmission</option>
                                    <option v-for="item in options.transmissions" :key="item.value" :value="item.value">{{ item.label }}</option>
                                </select>
                            </Field>
                            <Field label="Kilometres driven" :error="form.errors.km_driven"><input v-model="form.km_driven" class="customer-input" type="number" min="0" /></Field>
                            <Field label="Expected price" :error="form.errors.price"><input v-model="form.price" class="customer-input" type="number" min="0" /></Field>
                            <Field label="City" :error="form.errors.city"><input v-model="form.city" class="customer-input" /></Field>
                            <Field label="Registration number" :error="form.errors.registration_number"><input v-model="form.registration_number" class="customer-input uppercase" /></Field>
                            <Field label="Number of owners" :error="form.errors.owners">
                                <select v-model="form.owners" class="customer-input">
                                    <option v-for="owner in 4" :key="owner" :value="owner">{{ owner }}{{ ordinal(owner) }} owner</option>
                                </select>
                            </Field>
                        </div>
                    </section>

                    <section class="border-t border-slate-100 pt-7">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-950">Car images</h3>
                                <p class="mt-1 text-sm font-semibold text-slate-500">Choose 5 to 10 new images only when replacing the current set.</p>
                            </div>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <Images class="h-4 w-4" />
                                Select images
                                <input class="hidden" type="file" multiple accept="image/jpeg,image/png,image/gif" @change="selectImages" />
                            </label>
                        </div>

                        <div v-if="newImages.length" class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                            <button
                                v-for="(item, index) in newImages"
                                :key="item.url"
                                type="button"
                                class="group relative aspect-square overflow-hidden rounded-lg border-2 bg-slate-100"
                                :class="index === primaryIndex ? 'border-teal-500' : 'border-transparent'"
                                :title="index === primaryIndex ? 'Primary image' : 'Set as primary image'"
                                @click="primaryIndex = index"
                            >
                                <img :src="item.url" alt="" class="h-full w-full object-cover" />
                                <span v-if="index === primaryIndex" class="absolute left-2 top-2 rounded-md bg-teal-700 px-2 py-1 text-[10px] font-semibold text-white">Primary</span>
                                <span
                                    class="absolute right-2 top-2 grid h-7 w-7 place-items-center rounded-md bg-slate-950/75 text-white opacity-0 transition group-hover:opacity-100"
                                    role="button"
                                    aria-label="Remove image"
                                    @click.stop="removeImage(index)"
                                >
                                    <X class="h-4 w-4" />
                                </span>
                            </button>
                        </div>

                        <div v-else-if="listing.images.length" class="mt-5">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Current images</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                <div v-for="image in listing.images" :key="image" class="aspect-square overflow-hidden rounded-lg bg-slate-100">
                                    <img :src="image" alt="" class="h-full w-full object-cover" />
                                </div>
                            </div>
                        </div>
                        <p v-if="imageError" class="mt-3 text-sm font-bold text-red-600">{{ imageError }}</p>
                        <p v-if="form.errors.images" class="mt-3 text-sm font-bold text-red-600">{{ form.errors.images }}</p>
                    </section>

                    <section class="border-t border-slate-100 pt-7">
                        <h3 class="text-lg font-semibold text-slate-950">Owner and location</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <Field label="Email address" :error="form.errors.owner_email"><input v-model="form.owner_email" class="customer-input" type="email" /></Field>
                            <Field label="WhatsApp number" :error="form.errors.whatsapp_number"><input v-model="form.whatsapp_number" class="customer-input" inputmode="tel" /></Field>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <MapPin class="h-5 w-5 text-teal-700" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-950">{{ hasLocation ? 'Location attached' : 'Location not available' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ locationMessage }}</p>
                            </div>
                            <button type="button" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-50" :disabled="locating" @click="locate">
                                {{ locating ? 'Locating...' : 'Refresh location' }}
                            </button>
                        </div>
                    </section>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <Link :href="actions.dashboard" class="rounded-lg border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</Link>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-800 disabled:opacity-50" :disabled="form.processing">
                        <Save class="h-4 w-4" />
                        {{ form.processing ? 'Updating...' : 'Update listing' }}
                    </button>
                </div>
            </form>
        </div>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, onBeforeUnmount, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Images, MapPin, Save, X } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

type PreviewImage = { file: File; url: string };
const props = defineProps<{ listing: any; options: any; actions: { update: string; dashboard: string } }>();
const currentYear = new Date().getFullYear();
const newImages = ref<PreviewImage[]>([]);
const primaryIndex = ref(0);
const imageError = ref('');
const locating = ref(false);
const locationMessage = ref(props.listing.latitude && props.listing.longitude ? 'The saved listing location will be kept unless refreshed.' : 'Refresh location before submitting if local discovery is important.');

const form = useForm({
    title: props.listing.title || '',
    brand_id: props.listing.brand_id || '',
    model: props.listing.model || '',
    year: props.listing.year || '',
    fuel_type: props.listing.fuel_type || '',
    transmission: props.listing.transmission || '',
    km_driven: props.listing.km_driven || '',
    price: props.listing.price || '',
    city: props.listing.city || '',
    registration_number: props.listing.registration_number || '',
    owners: props.listing.owners || 1,
    owner_email: props.listing.owner_email || '',
    whatsapp_number: props.listing.whatsapp_number || '',
    latitude: props.listing.latitude || '',
    longitude: props.listing.longitude || '',
    images: [] as File[],
});

const hasLocation = computed(() => form.latitude !== '' && form.longitude !== '');
const ordinal = (value: number) => value === 1 ? 'st' : value === 2 ? 'nd' : value === 3 ? 'rd' : 'th';

const clearPreviews = () => {
    newImages.value.forEach((item) => URL.revokeObjectURL(item.url));
    newImages.value = [];
};

const selectImages = (event: Event) => {
    imageError.value = '';
    const files = Array.from((event.target as HTMLInputElement).files || []);
    if (files.length && (files.length < 5 || files.length > 10)) {
        imageError.value = 'Select between 5 and 10 images when replacing the current set.';
        (event.target as HTMLInputElement).value = '';
        return;
    }
    clearPreviews();
    newImages.value = files.map((file) => ({ file, url: URL.createObjectURL(file) }));
    primaryIndex.value = 0;
};

const removeImage = (index: number) => {
    URL.revokeObjectURL(newImages.value[index].url);
    newImages.value.splice(index, 1);
    primaryIndex.value = Math.min(primaryIndex.value, Math.max(0, newImages.value.length - 1));
    imageError.value = newImages.value.length && newImages.value.length < 5 ? 'At least 5 replacement images are required.' : '';
};

const locate = () => {
    if (!navigator.geolocation) {
        locationMessage.value = 'Geolocation is not supported by this browser.';
        return;
    }
    locating.value = true;
    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            locationMessage.value = 'Current location captured successfully.';
            locating.value = false;
        },
        () => {
            locationMessage.value = 'Location permission was not granted. The saved location will remain unchanged.';
            locating.value = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
    );
};

const submit = () => {
    if (newImages.value.length && newImages.value.length < 5) {
        imageError.value = 'At least 5 replacement images are required.';
        return;
    }
    const ordered = [...newImages.value];
    if (ordered.length && primaryIndex.value > 0) {
        const [primary] = ordered.splice(primaryIndex.value, 1);
        ordered.unshift(primary);
    }
    form.images = ordered.map((item) => item.file);
    form.transform((data) => ({ ...data, _method: 'put' })).post(props.actions.update, {
        forceFormData: true,
        preserveScroll: true,
    });
};

onBeforeUnmount(clearPreviews);

const Field = defineComponent({
    props: { label: { type: String, required: true }, error: { type: String, default: '' } },
    setup(fieldProps, { slots }) {
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, fieldProps.label),
            slots.default?.(),
            fieldProps.error ? h('span', { class: 'mt-1 block text-xs font-bold text-red-600' }, fieldProps.error) : null,
        ]);
    },
});
</script>
