<script setup lang="ts">
import ActionButton from '@/components/ActionButton.vue'
import PlusSolid from '@/assets/Icons/plus-solid.svg'

import { defineProps } from 'vue'

const props = defineProps<{
  title?: string
  description?: string
  image?: string
  type?: string
  id?: string
}>()
</script>

<template>
  <div class="flex flex-col lg:flex-row flex-1/4 bg-white rounded-xl justify-center">
    <div class="flex lg:flex-col px-5 py-3 items-center justify-between text-center gap-3 lg:gap-0">
      <div
        class="relative group max-w-23 lg:max-w-none lg:h-45 lg:w-45 rounded-full overflow-hidden"
      >
        <img
          :src="props.image"
          :alt="props.title"
          class="rounded-full w-full h-full object-cover"
        />
        <div
          class="absolute inset-0 bg-(--color-light-salmon) bg-opacity-30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center"
        >
          <div class="img-hover"></div>
        </div>
      </div>
      <div>
        <h3 class="text-center font-bold md:text-lg/6 lg:py-3">
          {{ props.title }}
        </h3>
        <p class="text-center lg:pb-3 hidden lg:block">
          {{ props.description }}
        </p>
      </div>
      <ActionButton
        :title="$t('knowMore')"
        :color="'--color-salmon'"
        :href="props.id"
        class="hidden lg:block"
      />
    </div>
    <ActionButton
      :title="$t('knowMore')"
      :color="'--color-salmon'"
      :href="props.id"
      class="lg:hidden justify-self-center"
    />
  </div>
</template>

<style scoped></style>