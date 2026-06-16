<template>
    <div aria-label="Progress">
        <ol role="list" class="flex items-center overflow-x-auto pb-7">
            <li v-for="(step, index) in steps" :key="step.title" :class="[index !== steps.length - 1 ? 'min-w-[78px] flex-1 pr-4 sm:pr-10' : 'min-w-[58px]', 'relative']">
                <div v-if="index < currentStep" class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="h-1.5 w-full bg-teal-700" />
                </div>
                <div v-else class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="h-1.5 w-full bg-slate-200" />
                </div>
                
                <span class="relative flex h-8 w-8 items-center justify-center rounded-full" 
                    :class="[
                        index < currentStep ? 'bg-teal-700 hover:bg-teal-800' : (index === currentStep ? 'border-2 border-teal-700 bg-white' : 'border-2 border-slate-300 bg-white hover:border-slate-400')
                    ]"
                    aria-current="step">
                    <svg v-if="index < currentStep" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    <span v-else class="text-sm font-semibold" :class="index === currentStep ? 'text-teal-700' : 'text-slate-500'">{{ index + 1 }}</span>
                </span>
                
                <span class="absolute -bottom-6 left-4 w-max -translate-x-1/2 text-xs font-semibold"
                    :class="index <= currentStep ? 'text-slate-950' : 'text-slate-400'">
                    {{ step.title }}
                </span>
            </li>
        </ol>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    steps: { title: string }[];
    currentStep: number;
}>();
</script>
