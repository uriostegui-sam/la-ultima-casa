<script setup lang="ts">
import NewsCarousel from '@/components/views/Visitors/News/NewsCarousel.vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import Title from '@/components/Title.vue'

const props = defineProps<{
  title?: string
  hasSubtitle?: boolean
  isWorkshop?: boolean
  subtitle?: string
  type?: string
  coverImage?: string
  artist?: string
  date?: string
  price?: number
  description?: string
}>()
</script>

<template>
    <Header />
    <section class="px-10 mx-auto max-w-7xl pb-10">
      <Title
        :title="props.title"
        :has-subtitle="props.hasSubtitle"
        :subtitle="props.subtitle"
        :type="props.type"
      />
      <div class="flex gap-5 lg:gap-20 flex-col-reverse lg:flex-row">
        <!-- :src="props.coverImage" -->
        <img
          src="https://picsum.photos/200"
          :alt="props.title"
          class="lg:flex-1/3 w-full h-full object-cover"
        />
        <div v-if="props.isWorkshop" class="lg:flex-2/3">
          <p>
            {{ props.artist }}
          </p>
          <p>
            {{ props.date }}
          </p>
          <p>
            ${{ props.price }} MXN
          </p>
          <p>
            {{ props.description }}
          </p>
        </div>
        <div v-if="!props.isWorkshop" class="lg:flex-2/3">
          <p>
            {{ props.description }}
          </p>
        </div>
      </div>
    </section>
    <NewsCarousel />
    <Footer />
</template>