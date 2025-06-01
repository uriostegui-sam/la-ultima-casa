<script setup lang="ts">
import type { Artwork } from '@/shared/Interfaces/Artwork'
import emblaCarouselVue from 'embla-carousel-vue'
import { onMounted, ref, watch } from 'vue'
import ChrevronSolid from '@/visitors/assets/Icons/chevron-solid.svg'

const props = defineProps<{
  artwork: Artwork
  title: string
  modal: boolean
}>()

const [emblaRef, emblaApi] = emblaCarouselVue({
  loop: false,
  align: 'start',
  slidesToScroll: 1,
})

const selectedIndex = ref(0)

onMounted(() => {
  emblaApi.value?.on('select', () => {
    selectedIndex.value = emblaApi.value?.selectedScrollSnap() ?? 0
  })
})

watch(emblaApi, (api) => {
  if (api) {
    selectedIndex.value = api.selectedScrollSnap()
    api.on('select', () => {
      selectedIndex.value = api.selectedScrollSnap()
    })
  }
})

watch(
  () => props.modal,
  (open) => {
    if (!open) {
      selectedIndex.value = 0
      emblaApi.value?.scrollTo(0)
    }
  },
)
</script>

<template>
  <div class="relative w-full max-w-4xl mx-auto">
    <div class="embla__viewport" ref="emblaRef">
      <div class="embla__container">
        <div v-for="(img, i) in props.artwork.images" :key="i" class="embla__slide">
            <!-- :src="img.url" -->
          <img
            src="https://picsum.photos/800"
            :alt="props.title"
            class="w-full object-contain max-h-[500px] mx-auto"
          />
        </div>
      </div>
    </div>
    <div v-if="props.artwork.images.length > 1" class="absolute inset-0 flex items-center justify-between px-2 pointer-events-none">
      <button
        v-if="selectedIndex > 0"
        @click="emblaApi?.scrollPrev()"
        class="embla__button before_button"
        type="button"
      >
        <ChrevronSolid class="w-3 h-3 fill-white" />
      </button>

      <button
        v-if="selectedIndex < props.artwork.images.length - 1"
        @click="emblaApi?.scrollNext()"
        class="embla__button after_button"
        type="button"
      >
        <ChrevronSolid class="reverse w-3 h-3 fill-white" />
      </button>
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
  background: var(--color-teal);
  color: white;
  padding: 0.3rem 0.3rem;
  cursor: pointer;
  border-radius: 100%;
  z-index: 12;
  pointer-events: auto;
}
.embla__button.before_button {
  left: 0;
}

.embla__button.after_button {
  right: 0;
}
</style>
