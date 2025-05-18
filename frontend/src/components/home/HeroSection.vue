<script setup lang="ts">
import Logo from '../Logo.vue'
import { useI18n } from 'vue-i18n'
import ActionButton from '../ActionButton.vue'
import ArrowRightIcon from '@/assets/Icons/arrow-right-solid.svg'
import { computed, onMounted, ref } from 'vue'

const { t } = useI18n()
const props = defineProps<{
  reverse?: boolean
  first?: boolean
  color?: string
}>()

const headerHeight = ref('4rem')
const descriptions = {
  adults:
    'Adults Pellentesque faucibus facilisis luctus. Quisque vel lacus vitae felis volutpat efficitur. Nunc porta, est ac semper dictum, erat diam dignissim arcu, a lobortis velit tellus vitae dui. Pellentesque quis nisi sed augue dignissim hendrerit. Ut dictum enim et magna dapibus, eget tincidunt orci pellentesque. Suspendisse dictum vel lacus eleifend consequat. Ut dapibus ornare nisl suscipit dapibus. Aliquam erat volutpat. Sed venenatis eget erat vitae tincidunt. Sed non luctus nibh.',
  kids: 'Kids Pellentesque faucibus facilisis luctus. Quisque vel lacus vitae felis volutpat efficitur. Nunc porta, est ac semper dictum, erat diam dignissim arcu, a lobortis velit tellus vitae dui. Pellentesque quis nisi sed augue dignissim hendrerit. Ut dictum enim et magna dapibus, eget tincidunt orci pellentesque. Suspendisse dictum vel lacus eleifend consequat. Ut dapibus ornare nisl suscipit dapibus. Aliquam erat volutpat. Sed venenatis eget erat vitae tincidunt. Sed non luctus nibh.',
}
const photos = {
  adults: 'https://picsum.photos/300',
  kids: 'https://picsum.photos/301',
}

const imageSrc = computed(() => {
  if (props.first) return 'https://picsum.photos/300'
  return props.reverse ? photos.adults : photos.kids
})

const description = computed(() => {
  if (props.first) return t('presentation')
  return props.reverse ? descriptions.adults : descriptions.kids
})

const buttonColor = computed(() => (props.reverse ? '--color-salmon' : '--color-teal'))
const buttonText = computed(() => (props.first ? t('join') : t('explore')))

onMounted(() => {
  const header = document.querySelector('header')
  if (header) {
    headerHeight.value = `${header.offsetHeight}px`
  }
})
</script>

<template>
  <div :class="props.reverse ? 'background' : ''">
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
      <div class="flex-1 pt-5" :class="!props.first && 'hidden lg:flex'">
        <img :src="imageSrc" alt="" class="w-full h-auto object-cover rounded-xl" />
      </div>

      <!-- Content -->
      <div class="flex-1 hero-content ">
        <div v-if="props.first" class="flex justify-between w-full items-center">
          <h1 class="flex-1 text-(--color-teal)">LA ÚLTIMA CASA</h1>
          <div class="flex-1 flex justify-end">
            <Logo :header="false" :hero="true" />
          </div>
        </div>

        <!-- Main heading for non-first -->
        <h2
          v-else
          class="font-title md:text-6xl text-4xl"
          :class="props.reverse ? 'text-(--color-salmon)' : 'text-(--color-teal)'"
        >
          {{ $t('coursesFor') }}
          <span class="font-title-alt">
            {{ props.reverse ? $t('adults') : $t('children') }}
          </span>
        </h2>

        <!-- Mobile image for non-first -->
        <div v-if="!props.first" class="flex-1 pt-5 lg:hidden">
          <img :src="imageSrc" alt="" class="w-full h-auto object-cover rounded-xl" />
        </div>

        <p class="py-6 text-lg" v-html="description" />

        <div class="flex justify-end">
          <ActionButton :color="buttonColor" href="#">
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
