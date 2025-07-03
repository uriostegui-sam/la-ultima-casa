<script setup lang="ts">
import Logo from '../Logo.vue'
import { useI18n } from 'vue-i18n'
import ActionButton from '../ActionButton.vue'
import ArrowRightIcon from '@/visitors/assets/Icons/arrow-right-solid.svg'
import { computed, onMounted, ref } from 'vue'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useAdminAboutUsStore } from '@/admin/stores/AboutUsAdminStore'
import type { AboutUs } from '@/shared/Interfaces/AboutUs'
import { Languages, locale } from '@/shared/services/Translation'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import { useWorkshopStore } from '@/shared/stores/WorkshopStore'

const { t } = useI18n()
const props = defineProps<{
  reverse?: boolean
  first?: boolean
  color?: string
}>()

const baseUrl = import.meta.env.VITE_STORAGE_URL
const currentLang = locale
const aboutUsStore = useAdminAboutUsStore()
const workshopsStore = useWorkshopStore()
const headerHeight = ref('4rem')
const existingId = computed(() => {
  return Number(aboutUsStore.aboutUs.length > 0 ? aboutUsStore.aboutUs[0].id : null)
})
const aboutUs = ref<AboutUs | null>(null)
const workshopTransformed = computed(() => {
  return workshopsStore.workshops.map((workshop) => ({
    ...workshop,
  }))
})
const firstWorkshop = computed(
  () => workshopTransformed.value.find((w) => w.featured_position === 1) || null,
)
const secondWorkshop = computed(
  () => workshopTransformed.value.find((w) => w.featured_position === 2) || null,
)

const photos = {
  first: `${baseUrl}/${firstWorkshop.value?.cover_image_path}`,
  second: `${baseUrl}/${secondWorkshop.value?.cover_image_path}`,
}

const imageSrc = computed(() => {
  if (props.first)
    return aboutUs.value
      ? `${baseUrl}/${aboutUs.value.cover_image}`
      : 'https://picsum.photos/301'
  return props.reverse ? photos.first : photos.second
})

const firstTitle = computed(() =>
  currentLang.value === Languages.English
    ? firstWorkshop.value?.title?.en
    : firstWorkshop.value?.title?.es,
)

const secondTitle = computed(() =>
  currentLang.value === Languages.English
    ? secondWorkshop.value?.title?.en
    : secondWorkshop.value?.title?.es,
)

const firstDescription = computed(() =>
  currentLang.value === Languages.English
    ? firstWorkshop.value?.description?.en
    : firstWorkshop.value?.description?.es,
)

const secondDescription = computed(() =>
  currentLang.value === Languages.English
    ? secondWorkshop.value?.title?.en
    : secondWorkshop.value?.title?.es,
)

const aboutUsDescription = computed(() => {
  return currentLang.value === Languages.English
    ? (aboutUs.value?.description?.en ?? null)
    : (aboutUs.value?.description?.es ?? null)
})

const description = computed(() => {
  if (props.first) return aboutUsDescription.value
  return props.reverse ? firstDescription.value : secondDescription.value
})

const buttonColor = computed(() => (props.reverse ? '--color-salmon' : '--color-teal'))
const buttonText = computed(() =>
  props.first ? capitalizeFirstLetter(t('join')) : capitalizeFirstLetter(t('explore')),
)

onMounted(async () => {
  await aboutUsStore.getAboutUs()
  await workshopsStore.getFeaturedWorkshops()

  if (existingId.value) {
    await aboutUsStore.getAboutUsById(existingId.value)

    aboutUs.value = aboutUsStore.selectedAboutUs
    const header = document.querySelector('header')

    if (header) {
      headerHeight.value = `${header.offsetHeight}px`
    }
  }
})
</script>

<template>
  <div :class="props.reverse ? 'background md:mt-6' : ''">
    <div
      class="flex px-10 md:gap-10 items-center max-w-7xl mx-auto"
      :class="[
        props.first ? 'md:py-0 flex-col-reverse md:flex-row pb-10' : 'py-20',
        props.reverse ? 'lg:flex-row-reverse' : '',
      ]"
      :style="
        props.first
          ? `min-height: calc(100vh - ${headerHeight})`
          : `min-height: calc(80vh - ${headerHeight})`
      "
    >
      <!-- Image -->
      <div
        class="flex-1 pt-5 flex items-center justify-center"
        :class="!props.first && 'hidden lg:flex'"
      >
        <div v-if="aboutUs">
          <img :src="imageSrc" alt="" class="w-full h-auto object-cover rounded-xl" />
        </div>
        <div v-else>
          <LoadingComponent />
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 hero-content">
        <div v-if="props.first" class="flex justify-between w-full items-center">
          <h1 class="flex-1 text-(--color-teal)">LA ÚLTIMA CASA</h1>
          <div class="flex-1 flex justify-end">
            <Logo :header="false" :hero="true" />
          </div>
        </div>

        <!-- Main heading for non-first -->
        <h2
          v-if="!props.first"
          :class="props.reverse ? 'text-(--color-salmon)' : 'text-(--color-teal)'"
          class="font-title md:text-6xl text-4xl"
        >
          {{ props.reverse ? firstTitle : secondTitle }}
        </h2>

        <!-- Mobile image for non-first -->
        <div v-if="!props.first" class="flex-1 pt-5 lg:hidden">
          <img :src="imageSrc" alt="" class="w-full h-auto object-cover rounded-xl" />
        </div>

        <p class="py-6 text-lg" v-html="description" />

        <div class="flex justify-end">
          <ActionButton :color="buttonColor" href="/workshops">
            <div class="flex items-center">
              {{ buttonText }}
              <ArrowRightIcon class="ml-2 w-4 h-4 fill-white" />
            </div>
          </ActionButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-content h1 {
  font-family: 'NATS-Regular', sans-serif;
  font-size: 85px;
  font-style: normal;
  line-height: 65px;
}

.background {
  background-color: var(--color-light-salmon);
}

@media (max-width: 451px) {
  .hero-content h1 {
    font-size: 63px;
    line-height: 42px;
    margin-right: 1rem;
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .hero-content h1 {
    font-size: 85px;
    line-height: 65px;
  }
}

@media (min-width: 1024px) {
  .hero-content h1 {
    font-size: 85px;
    line-height: 65px;
  }
}
</style>
