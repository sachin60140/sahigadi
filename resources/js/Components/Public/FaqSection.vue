<template>
    <section class="py-16 md:py-24 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#0f172a] mb-4 tracking-tight">Frequently Asked Questions</h2>
                <p class="text-lg text-gray-500 font-medium">Got questions? We've got answers.</p>
            </div>
            
            <div class="space-y-4">
                <div v-for="(faq, index) in faqs" :key="index" class="border border-gray-200 rounded-lg overflow-hidden transition-all duration-300" :class="{'shadow-md border-[#0f766e]/20': activeIndex === index}">
                    <button @click="toggleFaq(index)" class="w-full px-6 py-5 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                        <span class="font-bold text-gray-900 pr-4" :class="{'text-[#0f766e]': activeIndex === index}">{{ faq.question }}</span>
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-colors" :class="activeIndex === index ? 'bg-blue-100 text-[#0f766e]' : 'bg-gray-100 text-gray-500'">
                            <svg class="w-5 h-5 transition-transform duration-300" :class="{'rotate-180': activeIndex === index}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <div class="overflow-hidden transition-all duration-300" :class="activeIndex === index ? 'max-h-[500px] opacity-100' : 'max-h-0 opacity-0'">
                        <div class="px-6 pb-6 pt-2 text-gray-600 text-sm leading-relaxed border-t border-gray-100">
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const activeIndex = ref<number | null>(0);

const toggleFaq = (index: number) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};

const faqs = [
    {
        question: "How does SahiGadi verify cars?",
        answer: "Every car listed as 'Verified' on SahiGadi goes through a comprehensive multi-point inspection by our expert evaluators. We check the engine, transmission, body panels, tires, and internal electronics to ensure the car meets our high-quality standards. We also verify all legal documents."
    },
    {
        question: "Can I test drive a car before buying?",
        answer: "Yes, absolutely! Once you find a car you like, you can contact the seller or dealer to schedule a test drive at a mutually convenient location and time before making any purchasing decision."
    },
    {
        question: "How do I contact the seller?",
        answer: "To ensure safety and privacy, we use an OTP-based contact system. Simply click 'View Contact' on the car details page, enter your mobile number, verify via OTP, and you'll get the seller's verified contact details instantly."
    },
    {
        question: "Are there any hidden fees?",
        answer: "No, SahiGadi believes in complete transparency. The prices you see on the platform are set by the sellers. We do not charge buyers any hidden commission or platform fees."
    }
];
</script>

