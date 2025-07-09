<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { Languages, locale } from '@/shared/services/Translation'
import { useSkillStore } from '@/shared/stores/SkillStore'
import { onMounted, computed } from 'vue'

const current = locale
const baseUrl = import.meta.env.VITE_STORAGE_URL
const skillStore = useSkillStore()
const skillTransformed = computed(() => {
  return skillStore.skills.map((skill) => ({
    ...skill,
  }))
})

onMounted(async () => {
  await skillStore.getSkills()
})
</script>

<template>
  <section class="pb-15 pt-5 px-10 max-w-9xl mx-auto">
    <div class="container bg-(--color-light-salmon) rounded-xl lg:px-15 px-5 py-16 mx-auto">
      <h2 class="section-title text-center font-title md:text-5xl text-2xl pb-5 lg:pb-14">{{ capitalizeFirstLetter($t('ourTechniques'))}}</h2>
      <div class="flex flex-wrap justify-center gap-y-3">
        <div
            v-for="(skill, index) in skillTransformed"
            :key="index"
            class="flex-1/4"
        >
            <div class="flex flex-col items-center">
               <img class="lg:h-20 h-17 lg:w-20 w-17 rounded-full object-cover" :src="`${baseUrl}/${skill.profile_image}`" :alt="current === Languages.English ? skill.name['en'] : skill.name['es']" />
               <h3 class="text-center">{{ current === Languages.English ? capitalizeFirstLetter(skill.name['en']) : capitalizeFirstLetter(skill.name['es']) }}</h3>
            </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
