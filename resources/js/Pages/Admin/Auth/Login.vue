<template>
    <Head title="Admin Login" />

    <main class="min-h-screen bg-[#f6f8fb] px-4 py-8 text-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto grid min-h-[calc(100vh-4rem)] max-w-6xl overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl lg:grid-cols-[0.9fr_1.1fr]">
            <section class="flex flex-col justify-between bg-teal-800 p-6 text-white sm:p-10 lg:p-12">
                <div>
                    <a :href="actions.home" class="inline-flex items-center gap-3" aria-label="SahiGadi home">
                        <span class="grid h-12 w-12 place-items-center rounded-lg bg-white text-lg font-semibold text-teal-800">SG</span>
                        <span>
                            <span class="block text-2xl font-semibold leading-none">SAHI<span class="text-orange-400">GADI</span></span>
                            <span class="mt-1 block text-xs font-semibold uppercase tracking-wide text-teal-100">Administration</span>
                        </span>
                    </a>

                    <div class="mt-14 max-w-md">
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-300">Operations access</p>
                        <h1 class="mt-3 text-4xl font-semibold leading-tight sm:text-5xl">Manage inventory, partners and revenue with clarity.</h1>
                        <p class="mt-5 text-sm font-semibold leading-7 text-teal-50">
                            Secure access for the SahiGadi administration team.
                        </p>
                    </div>
                </div>

                <div class="mt-12 grid gap-3 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                    <AccessPoint number="01" label="Inventory" />
                    <AccessPoint number="02" label="Finance" />
                    <AccessPoint number="03" label="Operations" />
                </div>
            </section>

            <section class="flex items-center p-6 sm:p-10 lg:p-14">
                <div class="mx-auto w-full max-w-md">
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Authorized users</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Sign in to admin.</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">Use your administrator email address and password.</p>

                    <div v-if="flashSuccess" class="mt-6 rounded-lg border border-teal-100 bg-teal-50 px-4 py-3 text-sm font-bold text-teal-800">
                        {{ flashSuccess }}
                    </div>
                    <div v-if="flashError" class="mt-6 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        {{ flashError }}
                    </div>

                    <form class="mt-7 grid gap-5" @submit.prevent="submit">
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Email address</span>
                            <input
                                v-model="form.email"
                                class="admin-input"
                                type="email"
                                autocomplete="username"
                                placeholder="admin@example.com"
                                required
                                autofocus
                            />
                            <span v-if="form.errors.email" class="mt-2 block text-xs font-bold text-red-700">{{ form.errors.email }}</span>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Password</span>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    class="admin-input pr-20"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    placeholder="Enter password"
                                    required
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-1 right-1 rounded-md px-3 text-xs font-semibold text-slate-500 transition hover:bg-white hover:text-teal-700"
                                    @click="showPassword = !showPassword"
                                >
                                    {{ showPassword ? 'Hide' : 'Show' }}
                                </button>
                            </div>
                            <span v-if="form.errors.password" class="mt-2 block text-xs font-bold text-red-700">{{ form.errors.password }}</span>
                        </label>

                        <button
                            type="submit"
                            class="mt-1 min-h-12 rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Signing in...' : 'Sign In' }}
                        </button>
                    </form>

                    <p class="mt-8 border-t border-slate-100 pt-5 text-xs font-semibold leading-5 text-slate-400">
                        Access is restricted to authorized SahiGadi administrators.
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    actions: { authenticate: string; home: string };
}>();

const page = usePage();
const showPassword = ref(false);
const flashSuccess = computed(() => (page.props as any).flash?.success || '');
const flashError = computed(() => (page.props as any).flash?.error || '');
const form = useForm({ email: '', password: '' });

const submit = () => {
    form.post(props.actions.authenticate, {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
    });
};

const AccessPoint = defineComponent({
    props: {
        number: { type: String, required: true },
        label: { type: String, required: true },
    },
    setup(accessProps) {
        return () => h('div', { class: 'border-t border-teal-600 pt-3' }, [
            h('p', { class: 'text-xs font-semibold text-orange-300' }, accessProps.number),
            h('p', { class: 'mt-1 text-sm font-semibold text-white' }, accessProps.label),
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
