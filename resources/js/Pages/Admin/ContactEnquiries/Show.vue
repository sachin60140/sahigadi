<template>
    <Head :title="`Contact message #${enquiry.id}`" />

    <AdminLayout :title="`Contact Message #${enquiry.id}`" eyebrow="Support inbox">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <Link href="/admin/contact-enquiries" class="inline-flex rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Back to messages
                    </Link>
                    <h2 class="mt-5 max-w-4xl text-3xl font-semibold text-slate-950">{{ enquiry.subject }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        Received {{ enquiry.created_at || 'N/A' }}
                        <span v-if="enquiry.created_relative"> / {{ enquiry.created_relative }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a :href="enquiry.reply_url" class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
                        Reply by email
                    </a>
                    <button type="button" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-white" @click="deleteEnquiry">
                        Delete message
                    </button>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Message content</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-950">Customer message</h3>
                <div class="mt-5 min-h-60 whitespace-pre-wrap rounded-lg border border-slate-100 bg-slate-50 p-5 text-sm font-semibold leading-8 text-slate-700">
                    {{ enquiry.message }}
                </div>
            </section>

            <section class="h-fit rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Sender</p>
                <div class="mt-4 flex items-center gap-3">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-teal-700 text-lg font-semibold uppercase text-white">{{ initial }}</span>
                    <div class="min-w-0">
                        <h3 class="truncate text-xl font-semibold text-slate-950">{{ enquiry.name }}</h3>
                        <span class="mt-1 inline-flex rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">Read</span>
                    </div>
                </div>

                <div class="mt-5 divide-y divide-slate-100 border-y border-slate-100">
                    <div class="py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</p>
                        <a :href="enquiry.email_url" class="mt-2 block break-all text-sm font-semibold text-teal-700">{{ enquiry.email }}</a>
                    </div>
                    <div class="py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Received date</p>
                        <p class="mt-2 text-sm font-semibold text-slate-950">{{ enquiry.created_date || 'N/A' }}</p>
                    </div>
                    <div class="py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Message ID</p>
                        <p class="mt-2 text-sm font-semibold text-slate-950">#{{ enquiry.id }}</p>
                    </div>
                </div>

                <a :href="enquiry.reply_url" class="mt-5 flex w-full justify-center rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                    Reply to {{ enquiry.name }}
                </a>
            </section>
        </section>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

type ContactEnquiry = {
    id: number;
    name: string;
    email: string;
    subject: string;
    message: string;
    is_read: boolean;
    created_at?: string | null;
    created_date?: string | null;
    created_relative?: string | null;
    email_url: string;
    reply_url: string;
    actions: { destroy: string };
};

const props = defineProps<{ enquiry: ContactEnquiry }>();
const initial = computed(() => props.enquiry.name?.trim().charAt(0) || '?');

const deleteEnquiry = () => {
    if (window.confirm('Delete this contact message permanently?')) {
        router.delete(props.enquiry.actions.destroy);
    }
};
</script>
