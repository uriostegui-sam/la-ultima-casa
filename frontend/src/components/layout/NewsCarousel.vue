<script setup>
import emblaCarouselVue from 'embla-carousel-vue'
import NewsCard from '@/components/NewsCard.vue'
import ActionButton from '@/components/ActionButton.vue'
import { ref, onMounted, computed } from 'vue'
import { useNewsStore } from '@/stores/NewsStore'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const [emblaRef, emblaApi] = emblaCarouselVue({
  loop: false,
  align: 'start',
  slidesToScroll: 1
})
const selectedIndex = ref(0)
const newsStore = useNewsStore()
const windowWidth = ref(window.innerWidth)

const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth
}

window.addEventListener('resize', updateWindowWidth)

onMounted(() => {
  updateWindowWidth()
})

const screenCategory = computed(() => {
  if (windowWidth.value >= 1024) return 'desktop'
  if (windowWidth.value >= 768) return 'tabletLarge'
  if (windowWidth.value >= 451) return 'tabletSmall'
  return 'mobile'
})

const peekedIndex = computed(() => {
  switch (screenCategory.value) {
    case 'desktop':
      return selectedIndex.value + 3
    case 'tabletLarge':
      return selectedIndex.value + 2
    case 'tabletSmall':
      return selectedIndex.value + 2
    case 'mobile':
    default:
      return selectedIndex.value + 1
  }
})

const scrollTo = (index) => {
  if (emblaApi.value) emblaApi.value.scrollTo(index)
}

const newsTransformed = computed(() => {
  return newsStore.news.slice(0, 5).map((news) => ({
    ...news,
  }))
})

onMounted(async () => {
  await newsStore.getNews()
})

onMounted(() => {
  if (!emblaApi.value) return

  const updateSelectedIndex = () => {
    selectedIndex.value = emblaApi.value.selectedScrollSnap()
  }

  emblaApi.value.on('select', updateSelectedIndex)
  updateSelectedIndex()
})
</script>

<template>
  <section class="max-w-7xl embla my-8 mx-auto ps-5">
    <div class="flex justify-between items-center py-3 xl:px-0 pe-5">
        <h1 class="text-(--color-teal)">{{ $t('latestNews') }}</h1>
        <ActionButton
          :title="$t('seeAll')"
          :color="'--color-teal'"
          :href="'/news'"
        />
    </div>
    <div class="embla__viewport" ref="emblaRef">
      <div class="embla__container">
        <div
          class="embla__slide"
          v-for="(newsItem, index) in newsTransformed"
          :key="newsItem.id"
          :class="{ 'blurred-slide': index === peekedIndex }"
        >
          <NewsCard
            :title="newsItem.title"
            :description="newsItem.content"
            :image="newsItem.image_url"
            :date="newsItem.published_at"
          />
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <div class="embla__controls">
      <button @click="emblaApi?.scrollNext()" class="embla__button after_button">→</button>
    </div>

    <!-- Dots -->
    <div class="embla__dots">
      <button
        v-for="index in emblaApi?.scrollSnapList().length || 0"
        :key="index"
        @click="scrollTo(index - 1)"
        :class="{ 'dot--selected': selectedIndex === index - 1 }"
        class="embla__dot"
      />
    </div>
  </section>
</template>

<style scoped>

h1 {
  font-family: 'NATS-Regular', sans-serif;
  font-size: 35px;
  font-style: normal;
  width: 50%;
  line-height: 20px;
}

.embla {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.embla__viewport {
  overflow: hidden;
  width: 100%;
}

.embla__container {
  display: flex;
}

.embla__slide {
  position: relative;
  flex: 0 0 90%;
  min-width: 0;
  padding-right: 1.2rem;
}

@media (min-width: 451px) and (max-width: 767px) {
  .embla__slide {
    flex: 0 0 calc(100% / 2.5);
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .embla__slide {
    flex: 0 0 calc(100% / 2.8);
  }
}

@media (min-width: 1024px) {
  .embla__slide {
    flex: 0 0 calc(100% / 3.3);
  }
}

.blurred-slide {
  filter: blur(4px);
  transition: filter 0.3s ease;
  pointer-events: none;
  opacity: 0.6;
  scale: 0.98;
}

.embla__slide__number {
  font-size: 5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 200px;
  background-color: #ddd;
}

/* Arrows */
.embla__controls {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.embla__button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: var(--color-teal);
  color: white;
  border: none;
  padding: 0.7rem 1rem;
  cursor: pointer;
  border-radius: 100%;
  z-index: 10;
  pointer-events: auto;
}

.embla__button.before_button {
  left: 1rem;
}

.embla__button.after_button {
  right: 1rem;
}

/* Dots */
.embla__dots {
  display: flex;
  justify-content: center;
  margin-top: 1rem;
}

.embla__dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin: 0 4px;
  background: #bbb;
  border: none;
  cursor: pointer;
}

.dot--selected {
  background: var(--color-teal);
}
</style>