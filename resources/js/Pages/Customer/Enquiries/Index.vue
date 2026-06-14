<template>
    <Head title="My Enquiries" />
    <CustomerLayout title="My Enquiries" eyebrow="Buyer contacts">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="text-xs font-black uppercase tracking-wide text-teal-700">Lead activity</p>
                <h2 class="mt-1 text-xl font-black text-slate-950">Unlocked buyer contacts</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Car</th><th class="px-5 py-3">Buyer</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="enquiry in enquiries.data" :key="enquiry.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-500">{{ enquiry.created_at }}</td>
                            <td class="px-5 py-4">
                                <a v-if="enquiry.car" :href="enquiry.car.url" target="_blank" rel="noopener noreferrer" class="font-black text-slate-950 hover:text-teal-700">{{ enquiry.car.title }}</a>
                                <p v-if="enquiry.car" class="mt-1 text-xs font-bold text-slate-500">{{ enquiry.car.unique_id }} · {{ enquiry.car.year || 'Year N/A' }} · {{ enquiry.car.registration_number || 'Registration N/A' }}</p>
                                <span v-else class="font-bold text-red-600">Car deleted</span>
                            </td>
                            <td class="px-5 py-4 font-black text-slate-900">{{ enquiry.customer_name }}</td>
                            <td class="px-5 py-4">
                                <a :href="`tel:${enquiry.customer_phone}`" class="inline-flex items-center gap-2 font-black text-teal-700"><Phone class="h-4 w-4" /> {{ enquiry.customer_phone }}</a>
                                <a v-if="enquiry.customer_email" :href="`mailto:${enquiry.customer_email}`" class="mt-1 flex items-center gap-2 text-xs font-bold text-slate-500"><Mail class="h-3.5 w-3.5" /> {{ enquiry.customer_email }}</a>
                            </td>
                            <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 rounded-md bg-teal-50 px-2.5 py-1 text-xs font-black text-teal-700 ring-1 ring-teal-100"><CircleCheck class="h-3.5 w-3.5" /> Contact unlocked</span></td>
                        </tr>
                        <tr v-if="!enquiries.data.length"><td colspan="5" class="px-5 py-14 text-center"><MessageSquareText class="mx-auto h-10 w-10 text-slate-300" /><p class="mt-3 text-lg font-black text-slate-950">No enquiries yet</p><p class="mt-2 text-sm font-semibold text-slate-500">Buyer contacts will appear here after they unlock your listing details.</p></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4"><PaginationLinks :links="enquiries.links" /></div>
        </section>
    </CustomerLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CircleCheck, Mail, MessageSquareText, Phone } from '@lucide/vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import PaginationLinks from '@/Components/Admin/PaginationLinks.vue';
defineProps<{ enquiries: any }>();
</script>
