<template>
    <div class="flex items-center space-x-2">
        <label for="sort" class="text-sm font-medium text-gray-700 whitespace-nowrap">Sort by:</label>
        <select 
            id="sort" 
            :value="modelValue"
            @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#071226] sm:text-sm sm:leading-6"
        >
            <option v-for="opt in options" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    modelValue: string;
    options: any[];
}>();
defineEmits(['update:modelValue']);
</script>
