<script setup lang="ts">
import NewsCarousel from '@/components/views/Visitors/News/NewsCarousel.vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import Title from '@/components/Title.vue'
import Facebook from '@/assets/Icons/facebook.svg'
import Instagram from '@/assets/Icons/instagram.svg'
import WorldWideWebSVG from '@/assets/Icons/world-wide-web.svg'
import type { Artwork } from '@/Interfaces/Artwork'

const props = defineProps<{
  title?: string
  hasSubtitle?: boolean
  isWorkshop?: boolean
  isArtist?: boolean
  subtitle?: string
  type?: string
  coverImage?: string
  artist?: string
  date?: string
  price?: number
  description?: string
  instagram?: string
  facebook?: string
  website?: string
  artworks?: Artwork[]
}>()

const socialLinks = [
  {
    name: 'facebook',
    icon: Facebook,
    url: props.facebook ? `https://www.facebook.com/${props.facebook}` : undefined,
    condition: !!props.facebook,
  },
  {
    name: 'instagram',
    icon: Instagram,
    url: props.instagram ? `https://www.instagram.com/${props.instagram}` : undefined,
    condition: !!props.instagram,
  },
  {
    name: 'website',
    icon: WorldWideWebSVG,
    url: typeof props.website === 'string' ? props.website : undefined,
    condition: typeof props.website === 'string',
  },
]

const getPrimaryImage = (artwork: Artwork) =>
  artwork.images.find((img) => img.is_primary)?.url ?? '';
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
      <div class="lg:flex-2/5 flex flex-col items-center">
        <!-- :src="props.coverImage" -->
        <img
          src="https://picsum.photos/200"
          :alt="props.title"
          class="w-full h-full object-cover"
        />
        <div
          v-if="props.isArtist"
          class="mt-3 bg-(--color-salmon) flex justify-around w-1/2 rounded-full py-2"
        >
          <template v-for="link in socialLinks" :key="link.name">
            <a v-if="link.condition" :href="link.url" target="_blank" rel="noopener noreferrer">
              <component :is="link.icon" class="w-7 h-7 fill-white" />
            </a>
          </template>
        </div>
      </div>
      <div v-if="props.isWorkshop" class="lg:flex-3/5">
        <p>
          {{ props.artist }}
        </p>
        <p>
          {{ props.date }}
        </p>
        <p>${{ props.price }} MXN</p>
        <p>
          {{ props.description }}
        </p>
      </div>
      <div v-if="!props.isWorkshop" class="lg:flex-3/5">
        <p>
          {{ props.description }}
        </p>
      </div>
    </div>
  </section>
  <section class="px-10 mx-auto max-w-7xl pb-10">
    <div v-if="props.isArtist" class="relative pt-10 grid grid-cols-3 gap-1 grid-flow-dense">
      <template v-for="(artwork, index) in props.artworks" :key="index">
        <div class="relative group overflow-hidden" :class="index === 4 ? 'col-span-2 row-span-2' : ''">
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
  <NewsCarousel />
  <Footer />
</template>
