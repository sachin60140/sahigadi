<template>
    <div>
        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-slate-300 px-4 py-8 transition-colors hover:border-teal-700 hover:bg-teal-50/50 sm:px-6 sm:py-10"
             @dragover.prevent="dragover = true"
             @dragleave.prevent="dragover = false"
             @drop.prevent="handleDrop"
             :class="{ 'border-teal-700 bg-teal-50/50': dragover }">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                </svg>
                <div class="mt-4 flex flex-col items-center justify-center gap-1 text-sm leading-6 text-slate-600 sm:flex-row sm:gap-0">
                    <label :for="id" class="relative cursor-pointer rounded-md bg-white font-black text-teal-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-teal-700 focus-within:ring-offset-2 hover:text-teal-800">
                        <span>Upload a file</span>
                        <input :id="id" :name="id" type="file" class="sr-only" multiple accept="image/jpeg,image/png,image/jpg" @change="handleFileSelect" />
                    </label>
                    <p class="sm:pl-1">or drag and drop</p>
                </div>
                <p class="text-xs leading-5 text-slate-500">PNG, JPG, JPEG up to 2MB. Minimum 5 images.</p>
            </div>
        </div>
        
        <p v-if="error" class="mt-1.5 text-sm font-medium text-red-600">{{ error }}</p>

        <!-- Preview Grid -->
        <div v-if="files.length > 0" class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
            <div v-for="(file, index) in files" :key="index" class="group relative aspect-[4/3] overflow-hidden rounded-lg border border-slate-200">
                <img :src="file.preview" class="h-full w-full object-cover" alt="Preview" @error="handleImageError" />
                
                <!-- Primary Badge -->
                <div v-if="primaryIndex === index" class="absolute left-2 top-2 z-10 rounded bg-teal-700 px-2 py-1 text-[10px] font-black text-white shadow-sm">
                    COVER
                </div>
                
                <div class="absolute inset-0 flex items-center justify-center space-x-2 bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                    <button type="button" @click.prevent="setPrimary(index)" title="Set as Cover" class="rounded-lg bg-white p-1.5 text-slate-700 transition-colors hover:bg-slate-100 hover:text-teal-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                    <button type="button" @click.prevent="removeFile(index)" title="Remove" class="rounded-lg bg-red-100 p-1.5 text-red-600 transition-colors hover:bg-red-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    id?: string;
    modelValue: File[];
    primaryIndex?: number;
    error?: string;
    maxFiles?: number;
}>();

const emit = defineEmits(['update:modelValue', 'update:primaryIndex']);

const dragover = ref(false);
const files = ref<{ file: File; preview: string }[]>([]);

// Sync internal state with props
watch(() => props.modelValue, (newVal) => {
    if (newVal.length === 0 && files.value.length > 0) {
        files.value = [];
    }
}, { deep: true });

const processFiles = (fileList: FileList | File[]) => {
    const max = props.maxFiles || 10;
    let added = 0;
    
    Array.from(fileList).forEach(file => {
        if (!file.type.match('image.*')) return;
        if (file.size > 2 * 1024 * 1024) return; // 2MB limit
        if (files.value.length + added >= max) return;
        
        added++;
        files.value.push({
            file,
            preview: URL.createObjectURL(file)
        });
    });
    
    emit('update:modelValue', files.value.map(f => f.file));
    if (files.value.length > 0 && props.primaryIndex === undefined) {
        emit('update:primaryIndex', 0);
    }
};

const handleDrop = (e: DragEvent) => {
    dragover.value = false;
    if (e.dataTransfer?.files) {
        processFiles(e.dataTransfer.files);
    }
};

const handleFileSelect = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        processFiles(target.files);
        target.value = ''; // Reset input
    }
};

const removeFile = (index: number) => {
    URL.revokeObjectURL(files.value[index].preview);
    files.value.splice(index, 1);
    emit('update:modelValue', files.value.map(f => f.file));
    
    if (props.primaryIndex === index) {
        emit('update:primaryIndex', 0);
    } else if (props.primaryIndex !== undefined && index < props.primaryIndex) {
        emit('update:primaryIndex', props.primaryIndex - 1);
    }
};

const setPrimary = (index: number) => {
    emit('update:primaryIndex', index);
};

const handleImageError = (e: Event) => {
    const img = e.target as HTMLImageElement;
    img.src = '/images/placeholder.png';
};
</script>
