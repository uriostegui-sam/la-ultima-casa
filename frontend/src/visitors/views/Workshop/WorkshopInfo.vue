<script setup lang="ts">
import { Languages, locale } from '@/shared/services/Translation'
import type { Workshop } from '@/shared/Interfaces/Workshop'
import { useI18n } from 'vue-i18n'
import { useWorkshopStore } from '@/shared/stores/WorkshopStore'
import { useRoute } from 'vue-router'
import { onMounted, computed, ref, watch } from 'vue'
import { capitalizeFirstLetter, formatDateRange } from '@/shared/services/Helpers'
import InfoComponent from '@/visitors/components/InfoComponent.vue'
import type { TranslatedSkill } from '@/shared/Interfaces/Skill'
import NewsCarousel from '../News/NewsCarousel.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'

const currentLang = ref(locale)
const route = useRoute()
const workshopStore = useWorkshopStore()
const currentWorkshop = ref<Workshop | null>(null)
const { t } = useI18n()
const formattedDate = ref('')
const baseUrl = import.meta.env.VITE_STORAGE_URL

const skillsTransformed = computed(() => {
  if (!currentWorkshop.value?.skills) return ''
  const skills = currentWorkshop.value.skills as TranslatedSkill[]

  const translatedNames = skills.map((skill) => 
    currentLang.value === Languages.English
      ? skill.en
      : skill.es
  )

  return capitalizeFirstLetter(translatedNames.join(', ').toLowerCase())
})

onMounted(async () => {
  const id = Number(route.params.id)

  await workshopStore.getWorkshop(id)
  currentWorkshop.value = workshopStore.selectedWorkshop
  updateFormattedDate()
})

function updateFormattedDate() {
  if (
    currentWorkshop.value &&
    currentWorkshop.value.type !== 'permanent' &&
    currentWorkshop.value.start_date &&
    currentWorkshop.value.end_date
  ) {
    formattedDate.value = formatDateRange(
      currentWorkshop.value.start_date.toISOString(),
      currentWorkshop.value.end_date.toISOString(),
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
        currentWorkshop.type === 'permanent'
          ? 'workshop.typeOfWorkshopPerm'
          : 'workshop.typeOfWorkshopTemp'
      "
      :cover-image="`${baseUrl}/${currentWorkshop.cover_image_path}`"
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
  <div v-else><LoadingComponent /></div>
  <NewsCarousel />
</template>

<style scoped></style>
