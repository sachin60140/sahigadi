<template>
    <div class="flex w-full items-center gap-2 md:w-auto">
        <label for="sort" class="whitespace-nowrap text-sm font-semibold text-slate-700">Sort</label>
        <select 
            id="sort" 
            :value="modelValue"
            @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            class="block h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-3 pr-9 text-sm font-semibold text-slate-800 outline-none transition focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-100 md:w-56"
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
