<template>
    <form class="grid gap-5" @submit.prevent="$emit('submit')">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-5 lg:grid-cols-[1fr_280px]">
                <div class="grid content-start gap-5">
                    <Field label="Brand name" :error="form.errors.name" required>
                        <input v-model="form.name" class="admin-input" required type="text" placeholder="Maruti Suzuki" />
                        <p class="mt-2 text-xs font-bold text-slate-500">The URL slug is generated automatically from this name.</p>
                    </Field>

                    <Field label="Brand logo" :error="logoError">
                        <input class="admin-file" accept="image/jpeg,image/png,image/gif" type="file" @change="selectLogo" />
                        <p class="mt-2 text-xs font-bold text-slate-500">JPG, PNG or GIF up to 2 MB. A square or horizontal transparent logo works best.</p>
                    </Field>

                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <input v-model="form.is_active" class="mt-1 h-5 w-5 accent-teal-700" type="checkbox" />
                        <span>
                            <span class="block text-sm font-black text-slate-950">Active brand</span>
                            <span class="mt-1 block text-xs font-semibold leading-5 text-slate-500">Available in vehicle listing forms and public filters.</span>
                        </span>
                    </label>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">Logo preview</p>
                    <div class="mt-4 grid aspect-square place-items-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-white p-6">
                        <img v-if="previewUrl" :src="previewUrl" :alt="form.name || 'Brand logo preview'" class="max-h-full max-w-full object-contain" />
                        <div v-else class="text-center">
                            <p class="text-3xl font-black text-slate-300">{{ brandInitial }}</p>
                            <p class="mt-2 text-xs font-bold text-slate-400">No logo selected</p>
                        </div>
                    </div>
                    <p class="mt-4 break-all text-sm font-black text-slate-700">{{ form.name || 'Brand name' }}</p>
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link :href="cancelUrl" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                Cancel
            </Link>
            <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-700 disabled:opacity-60" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, onBeforeUnmount, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    form: any;
    cancelUrl: string;
    submitLabel: string;
    currentLogoUrl?: string | null;
}>();

defineEmits<{ submit: [] }>();

const objectUrl = ref('');
const previewUrl = computed(() => objectUrl.value || props.currentLogoUrl || '');
const logoError = computed(() => props.form.errors.logo || '');
const brandInitial = computed(() => String(props.form.name || '?').trim().charAt(0).toUpperCase() || '?');

const selectLogo = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    props.form.logo = file;
    if (objectUrl.value) URL.revokeObjectURL(objectUrl.value);
    objectUrl.value = file ? URL.createObjectURL(file) : '';
};

onBeforeUnmount(() => {
    if (objectUrl.value) URL.revokeObjectURL(objectUrl.value);
});

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
.admin-input,
.admin-file {
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
</style>
