<script setup lang="ts">
import { Languages, locale } from '@/shared/services/Translation'
import type { Workshop } from '@/shared/Interfaces/Workshop'
import { useI18n } from 'vue-i18n'
import { useWorkshopStore } from '@/shared/stores/WorkshopStore'
import { useRoute } from 'vue-router'
import { onMounted, computed, ref, watch } from 'vue'
import { capitalizeFirstLetter, choseCurrentLanguage, formatDateRange } from '@/shared/services/Helpers'
import InfoComponent from '@/visitors/components/InfoComponent.vue'
import type { TranslatedSkill } from '@/shared/Interfaces/Skill'
import NewsCarousel from '../News/NewsCarousel.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import NotFoundLayout from '../NotFoundLayout.vue'

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
    choseCurrentLanguage(skill as Record<string, string>, currentLang.value)
  )

  return capitalizeFirstLetter(translatedNames.join(', ').toLowerCase())
})

function updateFormattedDate() {
  if (
    currentWorkshop.value &&
    currentWorkshop.value.type !== 'permanent' &&
    currentWorkshop.value.start_date &&
    currentWorkshop.value.end_date
  ) {
    const startDate = new Date (currentWorkshop.value.start_date);
    const endDate = new Date (currentWorkshop.value.end_date);
    formattedDate.value = formatDateRange(
      startDate.toISOString(),
      endDate.toISOString(),
      currentLang.value === Languages.English ? 'en' : 'es',
    )
  } else {
    formattedDate.value = ''
  }
}

onMounted(async () => {
  const id = Number(route.params.id)

  await workshopStore.getWorkshop(id)
  currentWorkshop.value = workshopStore.selectedWorkshop
  updateFormattedDate()
})


watch(locale, () => {
  updateFormattedDate()
})
</script>

<template>
  <div v-if="workshopStore.loading"><LoadingComponent /></div>
  <div v-else-if="currentWorkshop">
    <InfoComponent
      :is-workshop="true"
      :title="choseCurrentLanguage(currentWorkshop.title, currentLang)"
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
      :description="choseCurrentLanguage(currentWorkshop.description, currentLang)"
    />
  </div>
  <div v-else>
    <NotFoundLayout type="workshop" />
  </div>
  <NewsCarousel />
</template>

<style scoped></style>
