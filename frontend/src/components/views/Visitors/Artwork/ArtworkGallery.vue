<script setup lang="ts">
import type { Artwork } from '@/Interfaces/Artwork'
import emblaCarouselVue from 'embla-carousel-vue'
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import ChrevronSolid from '@/assets/Icons/chevron-solid.svg'
import XSolid from '@/assets/Icons/xmark-solid.svg'
import { capitalizeFirstLetter } from '@/Services/Helpers';
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/Services/Translation';
import InnerCarouselArtwork from './InnerCarouselArtwork.vue'

const { t } = useI18n()
const props = defineProps<{
  artist: string
  artworks?: Artwork[]
}>()

const current = locale
const modalOpen = ref(false)
const selectedIndex = ref(0)

const getPrimaryImage = (artwork: Artwork) =>
  artwork.images.find((img) => img.is_primary)?.url ?? ''

const [emblaRef, emblaApi] = emblaCarouselVue({
  loop: false,
  align: 'start',
  slidesToScroll: 1,
})

const closeModal = () => (modalOpen.value = false)

const openModal = (index: number) => {
  selectedIndex.value = index
  modalOpen.value = true
}

watch(modalOpen, async (open) => {
  if (open) {
    await nextTick()
    if (emblaApi.value) {
      emblaApi.value.reInit()
      emblaApi.value.scrollTo(selectedIndex.value)
    }
  }
})

watch(emblaApi, (api) => {
  if (api) {
    selectedIndex.value = api.selectedScrollSnap()
    api.on('select', () => {
      selectedIndex.value = api.selectedScrollSnap()
    })
  }
})

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') closeModal()
}

onMounted(() => {
  emblaApi.value?.on('select', () => {
    selectedIndex.value = emblaApi.value?.selectedScrollSnap() ?? 0
  })
  window.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>
  <section class="px-10 mx-auto max-w-7xl pb-10">
    <div class="relative pt-10 grid grid-cols-3 gap-1 grid-flow-dense">
      <template v-for="(artwork, index) in props.artworks" :key="index">
        <div
          class="relative group overflow-hidden"
          :class="index === 4 ? 'col-span-2 row-span-2' : ''"
          @click="openModal(index)"
        >
          <img
            :src="getPrimaryImage(artwork)"
            class="w-full h-full object-cover"
            :alt="artwork.title"
          />
          <div
            class="absolute inset-0 bg-(--color-light-salmon) opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center"
          >
            <div class="img-hover w-10 h-10"></div>
          </div>
        </div>
      </template>
    </div>
  </section>
  <div
    v-show="modalOpen"
    class="fixed inset-0 bg-(--color-light-salmon) bg-opacity-70 z-50 flex items-center justify-center"
  >
  <div class="w-5/6 m-auto py-15 relative bg-white">
    <XSolid class="close lg:w-10 w-6 lg:h-10 h-6 fill-(--color-salmon)" @click="closeModal()" />
    <section class="embla w-5/6 m-auto">
      <div class="embla__viewport" ref="emblaRef">
        <div class="embla__container">
          <template v-for="(artwork, index) in props.artworks" :key="index">
            <div class="embla__slide">
              <div class="flex flex-col lg:flex-row gap-10">
                <div class="flex-1">
                    <InnerCarouselArtwork
                        :artwork="artwork"
                        :title="artwork.title"
                        :modal="modalOpen"
                    />
                </div>
                <div class="flex-1">
                  <p class="text-(--color-salmon) md:text-xl text-lg">{{ props.artist }}</p>
                  <h3 class="font-title md:text-5xl text-2xl py-2">{{ artwork.title }}</h3>
                  <p class="pb-6">{{ current === Languages.English ? artwork.description['en'] : artwork.description['es'] }}</p>
                  <a class="font-bold text-(--color-salmon)">{{ capitalizeFirstLetter($t('knowMoreOf')) }} {{ props.artist }}</a>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </section>
    <div v-if="(props.artworks?.length ?? 0) > 1" class="embla__controls">
      <button v-if="selectedIndex > 0" @click="emblaApi?.scrollPrev()" class="embla__button p-2 lg:p-3 before_button" type="button">
        <ChrevronSolid class="lg:w-4 lg:h-4 w-3 h-3 fill-white" />
      </button>

      <button v-if="selectedIndex < ((props.artworks?.length ?? 0) - 1)" @click="emblaApi?.scrollNext()" class="embla__button p-2 lg:p-3 after_button" type="button">
        <ChrevronSolid class="reverse lg:w-4 lg:h-4 w-3 h-3 fill-white" />
      </button>
    </div>
  </div>
  </div>
</template>

<style scoped>
.embla__slide {
  flex: 0 0 100%;
}

.embla__button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: var(--color-salmon);
  color: white;
  border: 1px solid white;
  cursor: pointer;
  border-radius: 100%;
  z-index: 10;
  pointer-events: auto;
}

.dot--selected {
  background: var(--color-salmon);
}

.embla__button.before_button {
  left: -1.5rem;
}

.embla__button.after_button {
  right: -1.5rem;
}

.close {
  top: 2rem;
  right: 1rem;
  position: absolute;
  transform: translateY(-50%);
}
</style>
