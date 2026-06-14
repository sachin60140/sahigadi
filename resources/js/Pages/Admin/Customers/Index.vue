<template>
    <Head title="Manage Customers" />

    <AdminLayout title="Manage Customers" eyebrow="Customer operations">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-teal-700">Customer network</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">Manage registered customer profiles.</h2>
                    <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                        Review contact details, profile completion, wallet balance and customer-listed inventory.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[520px]">
                    <MetricTile label="Total" :value="stats.total" />
                    <MetricTile label="Strong profiles" :value="stats.complete_profiles" tone="teal" />
                    <MetricTile label="With listings" :value="stats.with_listings" tone="orange" />
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-[1fr_auto]" @submit.prevent="applyFilters">
                <label class="block">
                    <span class="mb-2 block text-sm font-black text-slate-700">Search</span>
                    <input v-model="filterForm.search" class="admin-input" type="search" placeholder="ID, name, email, phone or company" />
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="h-12 rounded-lg bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-teal-700">Filter</button>
                    <Link href="/admin/customers" class="grid h-12 place-items-center rounded-lg border border-slate-200 px-5 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                        Clear
                    </Link>
                </div>
            </form>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1080px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Contact</th>
                            <th class="px-5 py-3">Location</th>
                            <th class="px-5 py-3">Wallet</th>
                            <th class="px-5 py-3">Listings</th>
                            <th class="px-5 py-3">Profile</th>
                            <th class="px-5 py-3">Joined</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ customer.name }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ customer.customer_unique_id }}</p>
                                <p v-if="customer.company_name" class="mt-1 max-w-[240px] truncate text-xs font-semibold text-slate-500">{{ customer.company_name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-[240px] truncate font-semibold text-slate-700">{{ customer.email }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">{{ customer.phone || 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-700">{{ customer.city || 'N/A' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ customer.state || 'N/A' }}</p>
                            </td>
                            <td class="px-5 py-4 font-black text-teal-700">{{ formatCurrency(customer.wallet_balance) }}</td>
                            <td class="px-5 py-4 font-black text-slate-950">{{ customer.listings_count }}</td>
                            <td class="px-5 py-4">
                                <ProfileBar :value="customer.profile_completion_percentage" />
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ customer.joined_at || 'N/A' }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link :href="customer.show_url" class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-black text-teal-700 transition hover:bg-white">
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!customers.data.length">
                            <td colspan="8" class="px-5 py-14 text-center">
                                <p class="text-lg font-black text-slate-950">No customers found</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">Try another search term.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="customers.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type Customer = {
    id: number;
    customer_unique_id?: string | null;
    name: string;
    email: string;
    phone?: string | null;
    company_name?: string | null;
    city?: string | null;
    state?: string | null;
    wallet_balance: number;
    profile_completion_percentage: number;
    listings_count: number;
    joined_at?: string | null;
    show_url: string;
};

const props = defineProps<{
    customers: { data: Customer[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    filters: { search: string };
    stats: { total: number; complete_profiles: number; with_listings: number };
}>();

const filterForm = reactive({ search: props.filters.search || '' });

const formatCurrency = (value: number | string) => `Rs ${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(Number(value || 0))}`;

const applyFilters = () => {
    const params = filterForm.search ? { search: filterForm.search } : {};
    router.get('/admin/customers', params, { preserveScroll: true, preserveState: true });
};

const MetricTile = defineComponent({
    props: {
        label: { type: String, required: true },
        value: { type: Number, required: true },
        tone: { type: String, default: 'slate' },
    },
    setup(tileProps) {
        const classes = () => {
            if (tileProps.tone === 'teal') return 'border-teal-100 bg-teal-50 text-teal-700';
            if (tileProps.tone === 'orange') return 'border-orange-100 bg-orange-50 text-orange-700';
            return 'border-slate-200 bg-slate-50 text-slate-900';
        };

        return () => h('div', { class: ['rounded-lg border p-4', classes()] }, [
            h('p', { class: 'text-2xl font-black' }, tileProps.value),
            h('p', { class: 'mt-1 text-xs font-black uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const ProfileBar = defineComponent({
    props: { value: { type: Number, required: true } },
    setup(barProps) {
        return () => h('div', { class: 'min-w-[160px]' }, [
            h('div', { class: 'h-2 overflow-hidden rounded-full bg-slate-100' }, [
                h('div', {
                    class: ['h-full rounded-full', barProps.value >= 75 ? 'bg-teal-600' : 'bg-orange-500'],
                    style: { width: `${Math.min(100, Math.max(0, barProps.value))}%` },
                }),
            ]),
            h('p', { class: 'mt-1 text-xs font-bold text-slate-500' }, `${barProps.value}% completed`),
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
