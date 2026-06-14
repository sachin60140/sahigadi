<template>
    <Head>
        <title>{{ seoTitle || 'Contact SahiGadi - Used Car Support in Bihar' }}</title>
        <meta head-key="description" name="description" :content="seoDescription || 'Contact SahiGadi for used car buying help, selling support, dealer onboarding, listing questions and marketplace assistance across Bihar.'" />
        <meta head-key="keywords" name="keywords" content="contact SahiGadi, used car support Bihar, sell car support, dealer onboarding Bihar, used car marketplace help" />
        <meta head-key="robots" name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
        <link head-key="canonical" rel="canonical" href="/contact" />
        <meta head-key="og-title" property="og:title" :content="seoTitle || 'Contact SahiGadi - Used Car Support in Bihar'" />
        <meta head-key="og-description" property="og:description" :content="seoDescription || 'Contact SahiGadi for used car buying help, selling support, dealer onboarding, listing questions and marketplace assistance across Bihar.'" />
        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
    </Head>

    <PublicLayout>
        <section class="border-b border-slate-200 bg-[linear-gradient(135deg,#f7fbff_0%,#eefbf8_52%,#fff7ed_100%)]">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <nav class="mb-6 flex text-sm font-semibold text-slate-500" aria-label="Breadcrumb">
                    <ol class="inline-flex flex-wrap items-center gap-2">
                        <li><Link href="/" class="transition hover:text-teal-700">Home</Link></li>
                        <li class="text-slate-300">/</li>
                        <li class="font-black text-teal-700">Contact</li>
                    </ol>
                </nav>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-end">
                    <div>
                        <div class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg border border-orange-100 bg-white/85 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 shadow-sm">
                            <IconChat class="h-4 w-4" />
                            SahiGadi support
                        </div>
                        <h1 class="max-w-4xl text-3xl font-black leading-tight tracking-normal text-slate-950 sm:text-5xl">
                            Talk to us about buying, selling or listing cars.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                            Get help with used car search, seller listings, dealer onboarding, enquiries and marketplace questions across Bihar.
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70">
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600">Fast assistance</p>
                        <h2 class="mt-2 text-xl font-black leading-tight text-slate-950 sm:text-2xl">Choose the right support path.</h2>
                        <p class="mt-3 text-sm font-medium leading-7 text-slate-600">
                            Message us directly or use the quick links below to continue your buying or selling journey.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f7fbff] py-10 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="flashSuccess" class="mb-5 rounded-lg border border-teal-100 bg-teal-50 px-4 py-3 text-sm font-bold text-teal-700">
                    {{ flashSuccess }}
                </div>

                <div v-if="firstError" class="mb-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    Please check the form. {{ firstError }}
                </div>

                <div class="mb-5 grid gap-4 md:grid-cols-3">
                    <InfoCard title="Call us" detail="+91 98188 23408" href="tel:+919818823408" text="For urgent buyer, seller and dealer support.">
                        <IconPhone class="h-5 w-5" />
                    </InfoCard>
                    <InfoCard title="Email support" detail="support@sahigadi.com" href="mailto:support@sahigadi.com" text="Share listing, dealer or account questions.">
                        <IconMail class="h-5 w-5" />
                    </InfoCard>
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700">
                                <IconLocation class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-sm font-black text-slate-950">Office</h2>
                                <p class="mt-1 text-sm font-black text-slate-800">Awani Enterprises</p>
                                <p class="mt-2 text-sm font-medium leading-6 text-slate-600">A-5, Sector 65, Noida, UP</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
                    <aside class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <p class="text-xs font-black uppercase tracking-wide text-teal-700">Quick routes</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">Keep moving without waiting.</h2>
                        <p class="mt-3 text-sm font-medium leading-7 text-slate-600">Use these shortcuts if you already know what you need.</p>

                        <div class="mt-6 grid gap-3">
                            <QuickRoute href="/cars" title="Browse used cars" text="Search inventory by city, brand and budget.">
                                <IconSearch class="h-5 w-5" />
                            </QuickRoute>
                            <QuickRoute href="/sell-your-car" title="Sell your car" text="Create a clean listing for local buyers.">
                                <IconPlus class="h-5 w-5" />
                            </QuickRoute>
                            <QuickRoute href="/dealer/register" title="Register as dealer" text="Bring your showroom inventory online.">
                                <IconShop class="h-5 w-5" />
                            </QuickRoute>
                        </div>
                    </aside>

                    <form class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600">Send a message</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">Tell us what you need help with.</h2>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <Field label="Your Name" error-key="name">
                                <input v-model="form.name" type="text" class="sg-input" placeholder="Enter your name" autocomplete="name" />
                            </Field>
                            <Field label="Your Email" error-key="email">
                                <input v-model="form.email" type="email" class="sg-input" placeholder="you@example.com" autocomplete="email" />
                            </Field>
                            <Field wrapper-class="md:col-span-2" label="Subject" error-key="subject">
                                <input v-model="form.subject" type="text" class="sg-input" placeholder="Buying, selling, dealer support..." />
                            </Field>
                            <Field wrapper-class="md:col-span-2" label="Message" error-key="message">
                                <textarea v-model="form.message" class="sg-input min-h-[150px]" placeholder="Write your message here..." />
                            </Field>
                        </div>

                        <button
                            type="submit"
                            class="mt-5 inline-flex min-h-[50px] w-full items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                            <IconArrow class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, useSlots } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps<{
    seoTitle?: string;
    seoDescription?: string;
}>();

const page = usePage();
const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const flashSuccess = computed(() => (page.props as any).flash?.success || '');
const firstError = computed(() => Object.values(form.errors)[0] || '');

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const makeIcon = (path: string) => defineComponent({
    props: { class: { type: String, default: 'h-5 w-5' } },
    setup(props) {
        return () => h('svg', {
            class: props.class,
            fill: 'none',
            stroke: 'currentColor',
            viewBox: '0 0 24 24',
            'aria-hidden': 'true',
        }, [
            h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '2',
                d: path,
            }),
        ]);
    },
});

const IconChat = makeIcon('M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 0 1-4-.8L3 20l1.2-3.6A7.56 7.56 0 0 1 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z');
const IconPhone = makeIcon('M2 5.5C2 4.12 3.12 3 4.5 3h2A1.5 1.5 0 0 1 8 4.28l.4 2.4a1.5 1.5 0 0 1-.43 1.32l-.9.9a12 12 0 0 0 5.03 5.03l.9-.9a1.5 1.5 0 0 1 1.32-.43l2.4.4A1.5 1.5 0 0 1 18 16.5v2A2.5 2.5 0 0 1 15.5 21h-.5C7.82 21 2 15.18 2 8v-.5Z');
const IconMail = makeIcon('M3 8l8.4 5.6a1 1 0 0 0 1.2 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z');
const IconLocation = makeIcon('M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11ZM12 10.5h.01');
const IconSearch = makeIcon('m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z');
const IconPlus = makeIcon('M12 5v14m7-7H5');
const IconShop = makeIcon('M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m4-4h1m-1 4h1');
const IconArrow = makeIcon('M17 8l4 4m0 0-4 4m4-4H3');

const InfoCard = defineComponent({
    props: {
        title: { type: String, required: true },
        detail: { type: String, required: true },
        href: { type: String, required: true },
        text: { type: String, required: true },
    },
    setup(props) {
        const slots = useSlots();
        return () => h('div', { class: 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm' }, [
            h('div', { class: 'flex gap-3' }, [
                h('span', { class: 'grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700' }, slots.default?.()),
                h('div', [
                    h('h2', { class: 'text-sm font-black text-slate-950' }, props.title),
                    h('a', { href: props.href, class: 'mt-1 block text-sm font-black text-slate-800 transition hover:text-teal-700' }, props.detail),
                    h('p', { class: 'mt-2 text-sm font-medium leading-6 text-slate-600' }, props.text),
                ]),
            ]),
        ]);
    },
});

const QuickRoute = defineComponent({
    props: {
        href: { type: String, required: true },
        title: { type: String, required: true },
        text: { type: String, required: true },
    },
    setup(props) {
        const slots = useSlots();
        return () => h(Link, {
            href: props.href,
            class: 'group rounded-lg border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg',
        }, () => h('div', { class: 'flex items-center gap-3' }, [
            h('span', { class: 'grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-teal-50 text-teal-700 group-hover:bg-teal-700 group-hover:text-white' }, slots.default?.()),
            h('div', [
                h('h3', { class: 'text-sm font-black text-slate-950' }, props.title),
                h('p', { class: 'mt-1 text-sm font-medium leading-6 text-slate-600' }, props.text),
            ]),
        ]));
    },
});

const Field = defineComponent({
    props: {
        label: { type: String, required: true },
        errorKey: { type: String, required: true },
        wrapperClass: { type: String, default: '' },
    },
    setup(props) {
        const slots = useSlots();
        return () => h('label', { class: ['block', props.wrapperClass] }, [
            h('span', { class: 'mb-2 block text-sm font-black text-slate-700' }, props.label),
            slots.default?.(),
            form.errors[props.errorKey]
                ? h('span', { class: 'mt-2 block text-xs font-bold text-red-600' }, form.errors[props.errorKey])
                : null,
        ]);
    },
});
</script>

<style scoped>
.sg-input {
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
    transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}
.sg-input:focus {
    border-color: rgb(13 148 136);
    background: white;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
}
</style>
