<script setup lang="ts">
import Title from '@/visitors/components/Title.vue'
import CourseCard from '@/visitors/components/CourseCard.vue'
import MenuFilter from '@/visitors/components/MenuFilter.vue'
import { locale } from '@/shared/services/Translation'
import { useWorkshopStore } from '@/shared/stores/WorkshopStore'
import { onMounted, computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter, choseCurrentLanguage, normalizeSpaces } from '@/shared/services/Helpers'
import NewsCarousel from '@/visitors/views/News/NewsCarousel.vue'
import dashify from 'dashify'

const { t } = useI18n()
const current = locale
const workshopStore = useWorkshopStore()
const activeFilter = ref('all')
const baseUrl = import.meta.env.VITE_STORAGE_URL
const existsTemporary = ref(false)
const existsPermanent = ref(false)

const workshopTransformed = computed(() => {
  if (activeFilter.value === 'all') {
    return workshopStore.workshops.map((workshop) => ({
      ...workshop,
    }))
  }
  return workshopStore.workshops
    .filter((workshop) => workshop.type === activeFilter.value)
    .map((workshop) => ({
      ...workshop,
    }))
})

onMounted(async () => {
  await workshopStore.getWorkshops({active:1})
  
  existsPermanent.value = workshopTransformed.value.some(
    (workshop) => workshop.type === 'permanent'
  )
  existsTemporary.value = workshopTransformed.value.some(
    (workshop) => workshop.type === 'temporary'
  )

  activeFilter.value = existsPermanent.value && existsTemporary.value ? 'all' : existsPermanent.value ? 'permanent' : existsTemporary.value ? 'temporary' : '';
})
</script>

<template class="flex flex-col justify-between">
  <Title :title="capitalizeFirstLetter($t('workshop.workshops'))" />
  <MenuFilter :active="activeFilter" :temporary="existsTemporary" :permanent="existsPermanent" @change="activeFilter = $event" class="mb-12" />
  <section class="lg:pb-15 lg:pt-5 px-10 mx-auto max-w-screen-2xl">
    <div class="flex flex-wrap gap-y-7 gap-x-20">
      <CourseCard
        v-for="(workshop, index) in workshopTransformed"
        :key="index"
        :title="choseCurrentLanguage(workshop.title, current)"
        :description="normalizeSpaces(choseCurrentLanguage(workshop.description, current))"
        :image="`${baseUrl}/${workshop.cover_image_path}`"
        :type="workshop.type"
        :id="`/workshops/${dashify(choseCurrentLanguage(workshop.title, current))}/${workshop.id}`"
      />
    </div>
  </section>
  <NewsCarousel />
</template>

<style scoped></style>
