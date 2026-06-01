<template>
    <div class="min-h-screen flex flex-col font-sans">
        <Navbar />

        <main class="flex-grow">
            <slot />
        </main>

        <Footer />
    </div>
</template>

<script setup lang="ts">
import Navbar from '@/Components/Public/Navbar.vue';
import Footer from '@/Components/Public/Footer.vue';
</script>
