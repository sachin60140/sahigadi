<template>
    <div>
        <label v-if="label" :for="id" class="mb-1.5 block text-sm font-black text-slate-700">
            {{ label }} <span v-if="required" class="text-orange-500">*</span>
        </label>
        <div class="relative rounded-lg">
            <div v-if="$slots.prefix" class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <slot name="prefix"></slot>
            </div>
            
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                :class="[
                    'block h-12 w-full rounded-lg border px-4 text-sm font-semibold text-slate-800 outline-none transition',
                    $slots.prefix ? 'pl-10' : 'pl-4',
                    error 
                        ? 'border-red-300 bg-red-50/70 text-red-900 focus:border-red-500 focus:ring-4 focus:ring-red-100' 
                        : 'border-slate-200 bg-slate-50 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100 hover:border-slate-300'
                ]"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :maxlength="maxlength"
                :pattern="pattern"
                :autocomplete="autocomplete"
            />
            
            <div v-if="error" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-1.5 text-sm font-medium text-red-600">{{ error }}</p>
        <p v-else-if="helpText" class="mt-1.5 text-sm text-slate-500">{{ helpText }}</p>
    </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    id?: string;
    label?: string;
    type?: string;
    modelValue: string | number | null;
    error?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    helpText?: string;
    maxlength?: number | string;
    pattern?: string;
    autocomplete?: string;
}>(), {
    type: 'text',
    required: false,
    disabled: false,
});

defineEmits(['update:modelValue']);
</script>
