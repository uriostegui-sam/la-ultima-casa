<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const props = defineProps<{
  title: string
  isCreateMode?: boolean
  goBack?: boolean
}>()
</script>
<template>
  <div class="card" :class="props.goBack? 'grid grid-cols-3 items-center' : ''" >
    <a @click="$router.go(-1)" class="text-left" v-if="props.goBack">
        <Button type="submit" icon="pi pi-arrow-left" :label="capitalizeFirstLetter(t('commun.back'))" />
    </a>

    <h1 class="text-3xl text-center">
      {{ `${capitalizeFirstLetter(t(props.title))} (${isCreateMode ? t('commun.create') : t('commun.edit')})` }}
    </h1>

    <div></div>
  </div>
</template>
