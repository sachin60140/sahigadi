<template>
    <div>
        <label v-if="label" :for="id" class="block text-sm font-semibold text-gray-700 mb-1.5">
            {{ label }} <span v-if="required" class="text-[#f97316]">*</span>
        </label>
        <div class="relative rounded-md shadow-sm">
            <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <slot name="prefix"></slot>
            </div>
            
            <input
                :id="id"
                :type="showPassword ? 'text' : 'password'"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                :class="[
                    'block w-full rounded-xl border transition-all duration-200 py-3 pr-10',
                    $slots.prefix ? 'pl-10' : 'pl-4',
                    error 
                        ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 bg-red-50/50' 
                        : 'border-gray-300 focus:ring-[#f97316] focus:border-[#f97316] hover:border-gray-400 bg-white'
                ]"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :autocomplete="autocomplete"
            />
            
            <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
            >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>
        <p v-if="error" class="mt-1.5 text-sm font-medium text-red-600">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

withDefaults(defineProps<{
    id?: string;
    label?: string;
    modelValue: string;
    error?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    autocomplete?: string;
}>(), {
    required: false,
    disabled: false,
});

defineEmits(['update:modelValue']);

const showPassword = ref(false);
</script>

