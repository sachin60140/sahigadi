<template>
    <Head title="API Access" />
    <DealerLayout title="API Access" eyebrow="Developer tools">
        <div v-if="!api.globally_enabled || !api.enabled_for_dealer" class="rounded-lg border border-orange-100 bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-700">
            API access is currently unavailable for your account. Please contact SAHI GADI support.
        </div>

        <section v-if="newApiKey" class="mt-5 rounded-lg border-2 border-teal-600 bg-teal-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Your new API key</p>
            <p class="mt-1 text-sm font-medium text-teal-900">Copy it now — for your security it will never be shown again.</p>
            <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                <code class="min-w-0 flex-1 break-all rounded-lg bg-white px-3 py-3 font-mono text-xs text-slate-800 ring-1 ring-teal-200">{{ newApiKey }}</code>
                <button type="button" class="shrink-0 rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-800" @click="copy(newApiKey)">
                    {{ copied === newApiKey ? 'Copied' : 'Copy key' }}
                </button>
            </div>
        </section>

        <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Tile label="Cost per lookup" :value="money(api.charge)" />
            <Tile label="Wallet balance" :value="money(api.wallet_balance)" :tone="lowBalance ? 'red' : 'teal'" />
            <Tile label="API lookups" :value="String(usage.total)" />
            <Tile label="Spent via API" :value="money(usage.spent)" />
        </section>

        <div v-if="lowBalance" class="mt-4 flex flex-col gap-3 rounded-lg border border-red-100 bg-red-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-red-700">Your balance is below the cost of one lookup. API calls will fail until you recharge.</p>
            <Link :href="actions.wallet" class="shrink-0 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Recharge wallet</Link>
        </div>

        <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-bold text-slate-950">Using the API</h2>
                <p class="mt-1 text-sm font-medium text-slate-600">Send your key as a Bearer token. Each successful lookup debits {{ money(api.charge) }} from your wallet; repeat lookups of the same vehicle within 24 hours are free.</p>

                <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-500">Base URL</p>
                <div class="mt-2 flex items-center gap-2">
                    <code class="min-w-0 flex-1 break-all rounded-lg bg-slate-50 px-3 py-2 font-mono text-xs text-slate-800 ring-1 ring-slate-200">{{ api.base_url }}</code>
                    <button type="button" class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" @click="copy(api.base_url)">Copy</button>
                </div>

                <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-500">Example request</p>
                <div class="mt-2 overflow-x-auto rounded-lg bg-slate-950 p-4">
                    <pre class="font-mono text-xs leading-6 text-slate-100">{{ sample }}</pre>
                </div>
                <button type="button" class="mt-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" @click="copy(sample)">
                    {{ copied === sample ? 'Copied' : 'Copy example' }}
                </button>

                <p class="mt-6 text-xs font-semibold uppercase tracking-wide text-slate-500">Endpoints</p>
                <div class="mt-2 overflow-x-auto">
                    <table class="w-full min-w-[520px] text-left text-sm">
                        <thead class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr><th class="py-2">Method</th><th class="py-2">Path</th><th class="py-2">Billed</th><th class="py-2">Purpose</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <tr><td class="py-2 font-semibold text-teal-700">POST</td><td class="py-2 font-mono text-xs">/vehicle/rc</td><td class="py-2">Yes</td><td class="py-2">Look up RC details</td></tr>
                            <tr><td class="py-2 font-semibold text-slate-500">GET</td><td class="py-2 font-mono text-xs">/account/balance</td><td class="py-2">No</td><td class="py-2">Wallet balance and price</td></tr>
                            <tr><td class="py-2 font-semibold text-slate-500">GET</td><td class="py-2 font-mono text-xs">/vehicle/searches</td><td class="py-2">No</td><td class="py-2">Your lookup history</td></tr>
                            <tr><td class="py-2 font-semibold text-slate-500">GET</td><td class="py-2 font-mono text-xs">/vehicle/rc/{id}</td><td class="py-2">No</td><td class="py-2">Re-fetch a past result</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-xs font-medium text-slate-500">Rate limit: 60 requests per minute. Responses use standard status codes — 402 means insufficient wallet balance.</p>
            </div>

            <aside class="self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-950">Your API key</h2>
                <div v-if="api.has_key" class="mt-3 rounded-lg bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-800">Active key</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Created {{ api.key_created_at }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">Last used: {{ api.key_last_used_at || 'never' }}</p>
                </div>
                <p v-else class="mt-3 text-sm font-medium text-slate-600">You do not have an API key yet. Generate one to start integrating.</p>

                <div class="mt-4 grid gap-2">
                    <button
                        type="button"
                        class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:opacity-50"
                        :disabled="!canUse || form.processing"
                        @click="generate"
                    >
                        {{ api.has_key ? 'Regenerate key' : 'Generate API key' }}
                    </button>
                    <button
                        v-if="api.has_key"
                        type="button"
                        class="rounded-lg border border-red-200 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                        :disabled="form.processing"
                        @click="revoke"
                    >
                        Revoke key
                    </button>
                </div>
                <p class="mt-3 text-xs font-medium text-slate-500">Keep your key secret. Anyone with it can spend your wallet balance. Regenerating immediately invalidates the previous key.</p>
            </aside>
        </section>

        <section v-if="usage.recent.length" class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-950">Recent API lookups</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Vehicle</th><th class="px-5 py-3">Result</th><th class="px-5 py-3 text-right">Charge</th><th class="px-5 py-3">When</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="call in usage.recent" :key="call.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold uppercase text-slate-950">{{ call.registration_number }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-md px-2.5 py-1 text-xs font-semibold" :class="call.is_success ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-700'">
                                    {{ call.is_success ? 'Success' : 'Failed' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ money(call.charge) }}</td>
                            <td class="px-5 py-4 font-medium text-slate-500">{{ call.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </DealerLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import DealerLayout from '@/Layouts/DealerLayout.vue';

const props = defineProps<{
    api: { globally_enabled: boolean; enabled_for_dealer: boolean; charge: number; wallet_balance: number; base_url: string; has_key: boolean; key_created_at?: string | null; key_last_used_at?: string | null };
    usage: { total: number; successful: number; spent: number; recent: Array<any> };
    actions: { generate: string; revoke: string; wallet: string };
}>();

const page = usePage();
const form = useForm({});
const copied = ref('');

const newApiKey = computed(() => (page.props as any).flash?.api_key || '');
const canUse = computed(() => props.api.globally_enabled && props.api.enabled_for_dealer);
const lowBalance = computed(() => props.api.wallet_balance < props.api.charge);

const sample = computed(() => `curl -X POST ${props.api.base_url}/vehicle/rc \\
  -H "Authorization: Bearer YOUR_API_KEY" \\
  -H "Content-Type: application/json" \\
  -d '{"registration_number":"BR01AB1234"}'`);

const money = (value: number) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const copy = async (text: string) => {
    try {
        await navigator.clipboard.writeText(text);
        copied.value = text;
        window.setTimeout(() => { copied.value = ''; }, 1800);
    } catch (error) {
        // clipboard unavailable - user can select manually
    }
};

const generate = () => {
    const message = props.api.has_key
        ? 'Regenerate your API key? Your current key will stop working immediately.'
        : 'Generate an API key?';
    if (window.confirm(message)) {
        form.post(props.actions.generate, { preserveScroll: true });
    }
};

const revoke = () => {
    if (window.confirm('Revoke your API key? Any integration using it will stop working immediately.')) {
        form.post(props.actions.revoke, { preserveScroll: true });
    }
};

const Tile = defineComponent({
    props: { label: { type: String, required: true }, value: { type: String, required: true }, tone: { type: String, default: 'slate' } },
    setup(p) {
        const tones: Record<string, string> = {
            slate: 'border-slate-200 bg-white text-slate-950',
            teal: 'border-teal-100 bg-teal-50 text-teal-700',
            red: 'border-red-100 bg-red-50 text-red-700',
        };
        return () => h('div', { class: ['rounded-lg border p-4 shadow-sm', tones[p.tone] || tones.slate] }, [
            h('p', { class: 'text-2xl font-bold tracking-tight' }, p.value),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, p.label),
        ]);
    },
});
</script>
