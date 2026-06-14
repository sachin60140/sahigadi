<template>
    <div>
        <label v-if="label" :for="id" class="mb-1.5 block text-sm font-black text-slate-700">
            {{ label }} <span v-if="required" class="text-orange-500">*</span>
        </label>
        <div class="relative rounded-lg">
            <select
                :id="id"
                :value="modelValue"
                @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
                :class="[
                    'block h-12 w-full appearance-none rounded-lg border pl-4 pr-10 text-sm font-semibold text-slate-800 outline-none transition',
                    error 
                        ? 'border-red-300 bg-red-50/70 text-red-900 focus:border-red-500 focus:ring-4 focus:ring-red-100' 
                        : 'border-slate-200 bg-slate-50 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100 hover:border-slate-300'
                ]"
                :required="required"
                :disabled="disabled"
            >
                <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
                <slot></slot>
            </select>
            
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-1.5 text-sm font-medium text-red-600">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    id?: string;
    label?: string;
    modelValue: string | number | null;
    error?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
}>(), {
    required: false,
    disabled: false,
});

defineEmits(['update:modelValue']);
</script>
