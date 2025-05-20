<script setup lang="ts">
import { capitalizeFirstLetter } from '@/Services/Helpers'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps<{
  title?: string
  hasSubtitle?: boolean
  subtitle?: string
  type?: string
}>()
</script>

<template>
  <div class="text-center lg:py-13 pb-10 font-(family-name:--font-title)">
    <p v-if="props.type" class="lg:pb-3 pb-1 text-md">
      {{ capitalizeFirstLetter($t(props.type)) }}
    </p>
    <h1
      class="lg:text-4xl text-2xl font-bold text-(--color-salmon)"
    >
      {{ props.title }}
    </h1>
    <p v-if="hasSubtitle" class="lg:pt-3 pt-1 text-md lg:text-2xl text-(--color-grey)">
      {{ props.subtitle }}
    </p>
  </div>
</template>

<style scoped></style>
