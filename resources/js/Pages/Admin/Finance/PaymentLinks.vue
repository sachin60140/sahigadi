<template>
    <Head title="Payment Links" />

    <AdminLayout title="Payment Links" eyebrow="Custom collections">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Payment links</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-950">Generate and manage collection links.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Create links for registered dealers or direct customers, then track payment status from one clean table.
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 sm:w-fit"
                    @click="showCreate = true"
                >
                    Generate New Link
                </button>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Payee</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Purpose</th>
                            <th class="px-5 py-3">Link</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="link in paymentLinks.data" :key="link.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ link.created_date }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ link.created_time }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ link.payee_name }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ link.payee_detail || 'No detail' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ formatCurrency(link.amount) }}</td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-800">{{ link.purpose }}</p>
                                <span class="mt-2 inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold uppercase text-slate-600">{{ link.gateway }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex max-w-[280px] items-center gap-2">
                                    <input class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs font-semibold text-slate-600" :value="link.public_url" readonly />
                                    <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" @click="copyLink(link.public_url)">
                                        Copy
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-md px-2.5 py-1 text-xs font-semibold" :class="statusClass(link.status)">
                                    {{ formatLabel(link.status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        v-if="link.status === 'pending'"
                                        type="button"
                                        class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-white"
                                        @click="refreshLink(link.refresh_url)"
                                    >
                                        Sync
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-white"
                                        @click="deleteLink(link.delete_url)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!paymentLinks.data.length">
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No payment links found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Generate a link to start collecting custom payments.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="paymentLinks.links" />
            </div>
        </section>

        <div v-if="showCreate" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/60 p-3 sm:items-center sm:p-4">
            <button class="absolute inset-0" type="button" aria-label="Close modal" @click="showCreate = false"></button>
            <form class="relative max-h-[calc(100vh-1.5rem)] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-6" @submit.prevent="submit">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">New payment link</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-950">Generate payment link</h2>
                    </div>
                    <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600" @click="showCreate = false">Close</button>
                </div>

                <div class="mt-5 grid gap-4">
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Payee Type</span>
                        <select v-model="payeeType" class="admin-input">
                            <option value="customer">Direct Customer</option>
                            <option value="dealer">Registered Dealer</option>
                        </select>
                    </label>

                    <label v-if="payeeType === 'dealer'" class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Dealer</span>
                        <select v-model="form.dealer_id" class="admin-input" required>
                            <option value="">Choose dealer</option>
                            <option v-for="dealer in dealers" :key="dealer.id" :value="dealer.id">{{ dealer.label }}</option>
                        </select>
                    </label>

                    <div v-else class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Customer Name</span>
                            <input v-model="form.customer_name" class="admin-input" :required="payeeType === 'customer'" type="text" />
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Mobile</span>
                            <input v-model="form.customer_mobile" class="admin-input" :required="payeeType === 'customer'" type="text" />
                        </label>
                        <label class="block md:col-span-2">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Email</span>
                            <input v-model="form.customer_email" class="admin-input" type="email" />
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Amount</span>
                            <input v-model="form.amount" class="admin-input" min="1" step="0.01" required type="number" />
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Gateway</span>
                            <select v-model="form.gateway" class="admin-input" required>
                                <option value="any">Any Available Gateway</option>
                                <option value="razorpay">Razorpay</option>
                                <option value="phonepe">PhonePe</option>
                            </select>
                        </label>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Purpose</span>
                        <input v-model="form.purpose" class="admin-input" required type="text" placeholder="Wallet Recharge, Featured Listing..." />
                    </label>
                </div>

                <div v-if="firstError" class="mt-4 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    {{ firstError }}
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700" @click="showCreate = false">Cancel</button>
                    <button type="submit" class="rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-600" :disabled="form.processing">
                        {{ form.processing ? 'Generating...' : 'Generate Link' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type PaymentLinkRow = {
    id: string;
    created_date: string;
    created_time: string;
    payee_name: string;
    payee_detail: string;
    amount: number;
    purpose: string;
    gateway: string;
    status: string;
    public_url: string;
    refresh_url: string;
    delete_url: string;
};

const props = defineProps<{
    paymentLinks: { data: PaymentLinkRow[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    dealers: Array<{ id: number; label: string }>;
}>();

const showCreate = ref(false);
const payeeType = ref<'customer' | 'dealer'>('customer');
const form = useForm({
    dealer_id: '',
    customer_name: '',
    customer_email: '',
    customer_mobile: '',
    amount: '',
    purpose: '',
    gateway: 'any',
});

const firstError = computed(() => Object.values(form.errors)[0] || '');
const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;
const formatLabel = (value: string) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
const statusClass = (status: string) => {
    if (status === 'paid') return 'bg-teal-50 text-teal-700 ring-1 ring-teal-100';
    if (status === 'expired') return 'bg-red-50 text-red-700 ring-1 ring-red-100';
    return 'bg-orange-50 text-orange-700 ring-1 ring-orange-100';
};

watch(payeeType, (type) => {
    if (type === 'dealer') {
        form.customer_name = '';
        form.customer_email = '';
        form.customer_mobile = '';
    } else {
        form.dealer_id = '';
    }
});

const submit = () => {
    form.post('/admin/payment-links', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.gateway = 'any';
            payeeType.value = 'customer';
            showCreate.value = false;
        },
    });
};

const copyLink = async (url: string) => {
    await navigator.clipboard.writeText(url);
};

const refreshLink = (url: string) => {
    if (confirm('Check gateway for payment status updates?')) {
        router.post(url, {}, { preserveScroll: true });
    }
};

const deleteLink = (url: string) => {
    if (confirm('Are you sure you want to delete this payment link?')) {
        router.delete(url, { preserveScroll: true });
    }
};
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
