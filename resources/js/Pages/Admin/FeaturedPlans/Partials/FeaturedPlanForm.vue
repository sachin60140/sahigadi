<template>
    <form class="grid gap-5" @submit.prevent="$emit('submit')">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-5 lg:grid-cols-2">
                <Field label="Plan name" :error="form.errors.name" required>
                    <input v-model="form.name" class="admin-input" required type="text" placeholder="7 Day Spotlight" />
                </Field>
                <Field label="Duration in days" :error="form.errors.duration_days" required>
                    <input v-model="form.duration_days" class="admin-input" min="1" required type="number" />
                </Field>
                <Field label="Price" :error="form.errors.price" required>
                    <input v-model="form.price" class="admin-input" min="0" step="0.01" required type="number" />
                </Field>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <input v-model="form.is_active" class="mt-1 h-5 w-5 accent-teal-700" type="checkbox" />
                    <span>
                        <span class="block text-sm font-black text-slate-950">Active featured plan</span>
                        <span class="mt-1 block text-xs font-semibold leading-5 text-slate-500">Visible when users promote a vehicle listing.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link :href="cancelUrl" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">Cancel</Link>
            <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-teal-700 disabled:opacity-60" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps<{ form: any; cancelUrl: string; submitLabel: string }>();
defineEmits<{ submit: [] }>();

const Field = defineComponent({
    props: { label: { type: String, required: true }, error: { type: String, default: '' }, required: { type: Boolean, default: false } },
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
.admin-input:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
