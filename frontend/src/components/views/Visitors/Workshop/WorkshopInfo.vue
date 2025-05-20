<script setup lang="ts">
import { Languages, locale } from '@/Services/Translation/index.ts'
import type { Workshop } from '@/Interfaces/Workshop'
import { useI18n } from 'vue-i18n'
import { useWorkshopStore } from '@/stores/WorkshopStore'
import { useRoute } from 'vue-router'
import { onMounted, computed, ref, watch } from 'vue'
import { capitalizeFirstLetter, formatDateRange } from '@/Services/Helpers'
import InfoComponent from '@/components/layout/InfoComponent.vue'

const currentLang = locale
const route = useRoute()
const workshopStore = useWorkshopStore()
const currentWorkshop = ref<Workshop | null>(null)
const { t } = useI18n()
const formattedDate = ref('')

const skillsTransformed = computed(() => {
  if (!currentWorkshop.value?.skills) return ''

  const skills = currentWorkshop.value.skills

  return capitalizeFirstLetter(skills.toString().toLowerCase()).replaceAll(
    ',',
    ', ',
  )
})

onMounted(async () => {
  const id = Number(route.params.id)

  await workshopStore.getWorkshop(id)
  currentWorkshop.value = workshopStore.selectedWorkshop?.data
  updateFormattedDate()
})

function updateFormattedDate() {
  if (currentWorkshop.value && currentWorkshop.value.type !== 'permanent') {
    formattedDate.value = formatDateRange(
      currentWorkshop.value.start_date,
      currentWorkshop.value.end_date,
      currentLang.value === Languages.English ? 'en' : 'es',
    )
  } else {
    formattedDate.value = ''
  }
}

watch(locale, () => {
  updateFormattedDate()
})
</script>

<template>
  <div v-if="currentWorkshop">
    <InfoComponent
      :is-workshop="true"
      :title="
        currentLang === Languages.English
          ? currentWorkshop.title['en']
          : currentWorkshop.title['es']
      "
      :has-subtitle="true"
      :subtitle="skillsTransformed"
      :type="
        currentWorkshop.cover_image_url === 'permanent'
          ? 'typeOfWorkshopPerm'
          : 'typeOfWorkshopTemp'
      "
      :cover-image="currentWorkshop.cover_image_url"
      :artist="capitalizeFirstLetter(currentWorkshop.artist?.name)"
      :date="capitalizeFirstLetter(formattedDate)"
      :price="currentWorkshop.price"
      :description="
        currentLang === Languages.English
          ? currentWorkshop.description['en']
          : currentWorkshop.description['es']
      "
    />
  </div>
  <div v-else>Loading...</div>
</template>

<style scoped></style>
