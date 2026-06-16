<template>
    <Head title="Change Password" />

    <AdminLayout title="Change Password" eyebrow="Account security">
        <section class="mx-auto max-w-3xl">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Administrator credentials</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-950">Set a new password.</h2>
                <p class="mt-2 text-sm font-semibold leading-7 text-slate-600">
                    Confirm your current password, then choose a new password with at least six characters.
                </p>

                <form class="mt-7 grid gap-5" @submit.prevent="submit">
                    <PasswordField
                        id="current-password"
                        v-model="form.current_password"
                        label="Current password"
                        autocomplete="current-password"
                        :error="form.errors.current_password"
                    />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <PasswordField
                            id="new-password"
                            v-model="form.new_password"
                            label="New password"
                            autocomplete="new-password"
                            :error="form.errors.new_password"
                        />
                        <PasswordField
                            id="confirm-password"
                            v-model="form.new_password_confirmation"
                            label="Confirm new password"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-800">Password requirements</p>
                        <div class="mt-3 grid gap-2 text-xs font-bold sm:grid-cols-2">
                            <Requirement :met="form.new_password.length >= 6" label="At least 6 characters" />
                            <Requirement :met="passwordsMatch" label="Confirmation matches" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <Link :href="actions.cancel" class="grid min-h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="form.processing || !passwordReady"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Password' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    actions: { update: string; cancel: string };
}>();

const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const passwordsMatch = computed(() => Boolean(form.new_password) && form.new_password === form.new_password_confirmation);
const passwordReady = computed(() => Boolean(form.current_password) && form.new_password.length >= 6 && passwordsMatch.value);

const submit = () => form.post(props.actions.update, { preserveScroll: true });

const PasswordField = defineComponent({
    props: {
        id: { type: String, required: true },
        label: { type: String, required: true },
        modelValue: { type: String, required: true },
        autocomplete: { type: String, required: true },
        error: { type: String, default: '' },
    },
    emits: ['update:modelValue'],
    setup(fieldProps, { emit }) {
        const visible = ref(false);
        return () => h('label', { class: 'block' }, [
            h('span', { class: 'mb-2 block text-sm font-semibold text-slate-700' }, fieldProps.label),
            h('div', { class: 'relative' }, [
                h('input', {
                    id: fieldProps.id,
                    value: fieldProps.modelValue,
                    type: visible.value ? 'text' : 'password',
                    required: true,
                    minlength: fieldProps.id === 'current-password' ? undefined : 6,
                    autocomplete: fieldProps.autocomplete,
                    class: [
                        'min-h-12 w-full rounded-lg border bg-slate-50 px-4 py-3 pr-20 text-sm font-semibold text-slate-800 outline-none transition focus:bg-white focus:ring-4',
                        fieldProps.error
                            ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                            : 'border-slate-200 focus:border-teal-600 focus:ring-teal-100',
                    ],
                    onInput: (event: Event) => emit('update:modelValue', (event.target as HTMLInputElement).value),
                }),
                h('button', {
                    type: 'button',
                    class: 'absolute inset-y-1 right-1 rounded-md px-3 text-xs font-semibold text-slate-500 transition hover:bg-white hover:text-teal-700',
                    onClick: () => { visible.value = !visible.value; },
                }, visible.value ? 'Hide' : 'Show'),
            ]),
            fieldProps.error ? h('span', { class: 'mt-2 block text-xs font-bold text-red-700' }, fieldProps.error) : null,
        ]);
    },
});

const Requirement = defineComponent({
    props: { met: { type: Boolean, required: true }, label: { type: String, required: true } },
    setup(requirementProps) {
        return () => h('p', {
            class: requirementProps.met ? 'text-teal-700' : 'text-slate-500',
        }, `${requirementProps.met ? 'Ready' : 'Pending'}: ${requirementProps.label}`);
    },
});
</script>
