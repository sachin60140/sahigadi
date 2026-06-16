<template>
    <Head>
        <title>{{ title }} - SAHI GADI</title>
        <meta head-key="description" name="description" :content="description" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
        <link head-key="canonical" rel="canonical" :href="canonical" />
        <meta head-key="og-title" property="og:title" :content="`${title} - SAHI GADI`" />
        <meta head-key="og-description" property="og:description" :content="description" />
    </Head>

    <PublicLayout>
        <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f6fbff_0%,#eefbf7_55%,#fff8ef_100%)]">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm font-bold text-slate-500">
                    <Link href="/" class="hover:text-teal-700">Home</Link>
                    <ChevronRight class="h-4 w-4 text-slate-300" />
                    <span class="text-teal-700">{{ title }}</span>
                </nav>
                <div class="mt-7 grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-end">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-lg border border-teal-100 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-teal-700 shadow-sm">
                            <Scale class="h-4 w-4" />
                            Legal information
                        </span>
                        <h1 class="mt-5 text-3xl font-semibold leading-tight text-slate-950 sm:text-5xl">{{ title }}</h1>
                        <p class="mt-5 max-w-3xl text-base font-medium leading-8 text-slate-600 sm:text-lg">{{ description }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Last updated</p>
                        <p class="mt-2 text-xl font-semibold text-slate-950">{{ lastUpdated }}</p>
                        <p class="mt-3 text-sm font-medium leading-6 text-slate-600">Questions can be sent to support@sahigadi.com.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f7f9fb] py-10 sm:py-12">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[240px_minmax(0,1fr)] lg:px-8">
                <aside class="hidden lg:block">
                    <nav class="sticky top-24 rounded-lg border border-slate-200 bg-white p-4 shadow-sm" aria-label="Page sections">
                        <p class="px-2 text-xs font-semibold uppercase tracking-wide text-slate-400">On this page</p>
                        <div class="mt-3 grid gap-1">
                            <a
                                v-for="section in sections"
                                :key="section.number"
                                :href="`#section-${section.number}`"
                                class="rounded-lg px-2 py-2 text-sm font-bold text-slate-600 transition hover:bg-teal-50 hover:text-teal-700"
                            >
                                {{ section.number }}. {{ section.title }}
                            </a>
                        </div>
                    </nav>
                </aside>

                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:p-10">
                    <section
                        v-for="section in sections"
                        :id="`section-${section.number}`"
                        :key="section.number"
                        class="scroll-mt-24 border-b border-slate-100 py-7 first:pt-0 last:border-0 last:pb-0"
                    >
                        <div class="flex items-start gap-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-teal-50 text-sm font-semibold text-teal-700">{{ section.number }}</span>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-xl font-semibold text-slate-950 sm:text-2xl">{{ section.title }}</h2>
                                <p v-for="paragraph in section.paragraphs" :key="paragraph" class="mt-4 text-sm font-medium leading-7 text-slate-700 sm:text-base">
                                    {{ paragraph }}
                                </p>
                                <ul v-if="section.items.length" class="mt-4 grid gap-3">
                                    <li v-for="item in section.items" :key="item" class="flex gap-3 text-sm font-medium leading-7 text-slate-700 sm:text-base">
                                        <Check class="mt-1.5 h-4 w-4 shrink-0 text-teal-700" />
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                                <p v-if="section.footer" class="mt-4 text-sm font-medium leading-7 text-slate-700 sm:text-base">{{ section.footer }}</p>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Check, ChevronRight, Scale } from '@lucide/vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

type Section = {
    number: number;
    title: string;
    paragraphs: string[];
    items: string[];
    footer?: string | null;
};

defineProps<{
    title: string;
    description: string;
    canonical: string;
    lastUpdated: string;
    sections: Section[];
}>();
</script>
