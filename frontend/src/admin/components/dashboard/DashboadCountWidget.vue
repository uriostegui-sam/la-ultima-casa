<script setup lang="ts">
const props = defineProps<{
  title: string
  icon?: string
  count: number
}>();
</script>

<template>
    <div class="col-span-12 lg:col-span-6 xl:col-span-3">
        <div class="card mb-0">
            <div class="flex justify-between mb-4">
                <div>
                    <span class="block text-muted-color font-medium mb-4">{{ props.title }}</span>
                    <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">{{ props.count }}</div>
                </div>
                <div class="flex items-center justify-center bg-cyan-100 dark:bg-cyan-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                    <i :class="`pi ${props.icon} text-cyan-500 !text-xl`"></i>
                </div>
            </div>
        </div>
    </div>
</template>