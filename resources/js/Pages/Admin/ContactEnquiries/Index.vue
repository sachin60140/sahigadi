<template>
    <Head title="Contact Enquiries" />

    <AdminLayout title="Contact Enquiries" eyebrow="Support inbox">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Website messages</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-950">Keep the public contact inbox clear and current.</h2>
                <p class="mt-2 max-w-3xl text-sm font-semibold leading-7 text-slate-600">
                    Review contact form submissions, reply by email and track which messages still need attention.
                </p>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <MetricTile label="All messages" :value="stats.total" />
                <MetricTile label="Unread" :value="stats.unread" tone="orange" />
                <MetricTile label="Read" :value="stats.read" tone="teal" />
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Sender</th>
                            <th class="px-5 py-3">Subject</th>
                            <th class="px-5 py-3">Received</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="enquiry in enquiries.data" :key="enquiry.id" :class="enquiry.is_read ? 'hover:bg-slate-50' : 'bg-orange-50/40 hover:bg-orange-50/70'">
                            <td class="px-5 py-4"><ReadBadge :is-read="enquiry.is_read" /></td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ enquiry.name }}</p>
                                <a :href="enquiry.email_url" class="mt-1 block max-w-[260px] truncate text-xs font-bold text-teal-700">{{ enquiry.email }}</a>
                            </td>
                            <td class="px-5 py-4">
                                <Link :href="enquiry.actions.show" class="block max-w-[360px] truncate font-semibold text-slate-800 hover:text-teal-700">{{ enquiry.subject }}</Link>
                                <p class="mt-1 text-xs font-semibold text-slate-400">Message #{{ enquiry.id }}</p>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-600">{{ enquiry.created_at || 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="enquiry.actions.show" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">View</Link>
                                    <button
                                        v-if="!enquiry.is_read"
                                        type="button"
                                        class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-800"
                                        @click="markRead(enquiry)"
                                    >
                                        Mark read
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-white"
                                        @click="deleteEnquiry(enquiry)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!enquiries.data.length">
                            <td colspan="5" class="px-5 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No contact messages yet</p>
                                <p class="mt-2 text-sm font-semibold text-slate-500">New public contact form submissions will appear here.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <PaginationLinks :links="enquiries.links" />
            </div>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';

type ContactEnquiry = {
    id: number;
    name: string;
    email: string;
    subject: string;
    is_read: boolean;
    created_at?: string | null;
    email_url: string;
    actions: { show: string; read: string; destroy: string };
};

defineProps<{
    enquiries: { data: ContactEnquiry[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    stats: { total: number; unread: number; read: number };
}>();

const markRead = (enquiry: ContactEnquiry) => router.post(enquiry.actions.read, {}, { preserveScroll: true });
const deleteEnquiry = (enquiry: ContactEnquiry) => {
    if (window.confirm(`Delete the message from ${enquiry.name}?`)) {
        router.delete(enquiry.actions.destroy, { preserveScroll: true });
    }
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
            h('p', { class: 'text-2xl font-semibold' }, new Intl.NumberFormat('en-IN').format(tileProps.value)),
            h('p', { class: 'mt-1 text-xs font-semibold uppercase tracking-wide' }, tileProps.label),
        ]);
    },
});

const ReadBadge = defineComponent({
    props: { isRead: { type: Boolean, required: true } },
    setup(badgeProps) {
        return () => h('span', {
            class: [
                'inline-flex rounded-md px-2.5 py-1 text-xs font-semibold',
                badgeProps.isRead
                    ? 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'
                    : 'bg-orange-50 text-orange-700 ring-1 ring-orange-100',
            ],
        }, badgeProps.isRead ? 'Read' : 'Unread');
    },
});
</script>
