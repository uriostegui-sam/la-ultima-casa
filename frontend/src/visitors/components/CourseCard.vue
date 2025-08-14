<script setup lang="ts">
import ActionButton from '@/visitors/components/ActionButton.vue'
import { computed, defineProps } from 'vue'

const props = defineProps<{
  title?: string
  description?: string
  image?: string
  type?: string
  id?: string
}>()

const truncatedDescription = computed(() => {
  if (!props.description) return ''

  return props.description.length > 100 
    ? props.description.substring(0, 100) + '...' 
    : props.description
});
</script>

<template>
  <div class="flex flex-col md:flex-row md:flex-1/4 bg-white rounded-xl md:justify-center w-full">
    <div class="flex md:flex-col px-5 py-3 items-center md:justify-between text-center gap-3 md:gap-0">
      <div
        class="relative group flex-1/3 md:flex-none rounded-full overflow-hidden"
        >
        <img
          :src="props.image"
          :alt="props.title"
          class="rounded-full h-30 w-30 md:h-45 md:w-45 object-cover"
        />
        <router-link
          v-if="props.id"
          :to="props.id"
        >
          <div
          class="absolute inset-0 bg-(--color-light-salmon) bg-opacity-30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center"
          >
            <div class="img-hover"></div>
          </div>
        </router-link>
      </div>
      <div class="flex-2/3 md:flex-none">
        <h3 class="text-center font-bold md:text-md/6 md:py-3">
          {{ props.title }}
        </h3>
        <p class="text-center pb-3 block" v-html="truncatedDescription"/>
      </div>
      <ActionButton
        :title="'divers.knowMore'"
        :color="'--color-salmon'"
        :href="props.id"
        class="hidden md:block"
      />
    </div>
    <ActionButton
      :title="'divers.knowMore'"
      :color="'--color-salmon'"
      :href="props.id"
      class="md:hidden justify-self-center"
    />
  </div>
</template>

<style scoped></style>