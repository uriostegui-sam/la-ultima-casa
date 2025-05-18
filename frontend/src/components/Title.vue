<script setup lang="ts">
const props = defineProps<{
    title: string
}>()
</script>

<template>
    <h1 class="lg:py-13 pb-13 font-(family-name:--font-title) text-5xl font-bold text-center text-(--color-salmon)">
        {{ props.title }}
    </h1>
</template>

<style scoped></style>